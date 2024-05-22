<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\MemberModel;
use App\Models\PeriodModel;
use App\Models\User;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->search;
        $sort       = $request->sort ?? "member.created_at";
        $sort_type  = $request->sort_type ?? "desc";

        $query      = MemberModel::select('*')
            ->leftjoin('period', 'member.period_id', '=', 'period.period_id')
            ->leftjoin('division', 'member.division_id', '=', 'division.division_id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('member_nim', 'LIKE', '%' . $search . '%');
                $q->orWhere('member_name', 'LIKE', '%' . $search . '%');
                $q->orWhere('member_phone', 'LIKE', '%' . $search . '%');
                $q->orWhere('member_address', 'LIKE', '%' . $search . '%');
                $q->orWhere('member_email', 'LIKE', '%' . $search . '%');
            });
        }

        if (!empty($sort) && !empty($sort_type)) {
            $query->orderBy($sort, $sort_type);
        }

        $data = $query->get();
        return response()->json(APIFormatter::createApi(200, 'Success', $data));
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make(
                $params,
                [
                    'period_id'       => 'required | exists:period,period_id',
                    'member_nim'      => 'required | unique:member,member_nim',
                    'member_name'     => 'required',
                    'member_status'   => 'required | in:active,inactive,alumni',
                    'member_email'    => 'required | email | unique:member,member_email',
                    'member_password' => 'required | min:8 | regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', // at least 1 lowercase, 1 uppercase, 1 number
                    'structure_id'    => 'required',
                    'division_id'     => 'required'
                ],
                [
                    'period_id.required'       => 'Period ID is required.',
                    'period_id.exists'         => 'The selected period is invalid.',
                    'member_nim.required'      => 'NIM is required.',
                    'member_name.required'     => 'Name is required.',
                    'member_status.required'   => 'Status is required.',
                    'member_status.in'         => 'The selected status is invalid.',
                    'member_email.required'    => 'Email is required.',
                    'member_email.email'       => 'Email is invalid.',
                    'member_password.required' => 'Password is required.',
                    'member_password.min'      => 'Password must be at least 8 characters.',
                    'member_password.regex'    => 'Password must contain at least one lowercase letter, one uppercase letter, and one number.',
                    'structure_id.required'    => 'Strukture id is required',
                    'division_id.required'     => 'Division id is required'
                ]
            );

            if ($validator->fails()) {
                return response()->json(APIFormatter::createApi(400, 'Validation Error', $validator->errors()->all()));
            }

            if (MemberModel::where('member_nim', $params['member_nim'])->exists()) {
                return response()->json(APIFormatter::createApi(400, 'Member NIM already exists'));
            }

            if ($request->hasFile('member_image')) {
                $file_dir = public_path('/files/member/');
                if (!File::exists($file_dir)) {
                    File::makeDirectory($file_dir, $mode = 0777, true, true);
                }

                $image      = $request->file('member_image');
                $slug       = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));
                $image_name = "img_" . $slug . "_" . time() . "." . $image->getClientOriginalExtension();
                $image->move($file_dir, $image_name);
                $host       = env('APP_URL');
                $image_name = $host . '/public/files/member/' . $image_name;
            } else {
                $image_name = NULL;
            }

            $password = Hash::make($params['member_password']);

            $data = MemberModel::create([
                'period_id'        => $params['period_id'],
                'member_nim'       => $params['member_nim'],
                'member_name'      => $params['member_name'],
                'member_status'    => $params['member_status'],
                'member_birthdate' => $params['member_birthdate'],
                'member_address'   => $params['member_address'],
                'member_phone'     => $params['member_phone'],
                'member_email'     => $params['member_email'],
                'member_password'  => $password,
                'member_image_url' => $image_name,
                'structure_id'     => $params['structure_id'],
                'division_id'      => $params['division_id']
            ]);

            // AUTO REGISTER
            $user = User::create([
                'nim'       => $data->member_nim,
                'member_id' => $data->member_id,
                'name'      => $data->member_name,
                'email'     => $data->member_email,
                'password'  => $password,
            ]);

            return response()->json(APIFormatter::createApi(200, 'success', $data));
        } catch (\Exception $e) {
            return response()->json(APIFormatter::createApi(400, $e->getMessage()));
        }
    }

    public function show($id)
    {
        try {
            $data = MemberModel::where('member_id', $id)
                ->leftjoin('period', 'member.period_id', '=', 'period.period_id')
                ->leftjoin('division', 'division.division_id', '=', 'member.division_id')
                ->leftjoin('structure', 'structure.structure_id', '=', 'member.structure_id')
                ->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            return response()->json(APIFormatter::createApi(200, 'Success', $data));
        } catch (\Exception $e) {
            return response()->json(APIFormatter::createApi(400, $e->getMessage()));
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();

            $validator = Validator::make(
                $params,
                [
                    'period_id' => 'required | exists:period,period_id',
                    'member_nim' => 'required',
                    'member_name' => 'required',
                    'member_status' => 'required | in:active,inactive,alumni',
                    'member_email' => 'required | email',
                    'member_password' => 'required | min:8 | regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', // at least 1 lowercase, 1 uppercase, 1 number
                    'structure_id' => 'required',
                    'division_id' => 'required'
                ],
                [
                    'period_id.required' => 'Period ID is required.',
                    'period_id.exists' => 'The selected period is invalid.',
                    'member_nim.required' => 'NIM is required.',
                    'member_name.required' => 'Name is required.',
                    'member_status.required' => 'Status is required.',
                    'member_status.in' => 'The selected status is invalid.',
                    'member_email.required' => 'Email is required.',
                    'member_email.email' => 'Email is invalid.',
                    'member_password.required' => 'Password is required.',
                    'member_password.min' => 'Password must be at least 8 characters.',
                    'member_password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, and one number.',
                    'structure_id.required' => 'Strukture id is required',
                    'division_id.required' => 'Division id is required'
                ]
            );

            if ($validator->fails()) {
                return response()->json(APIFormatter::createApi(400, $validator->errors()->all()));
            }

            if ($request->hasFile('member_image')) {
                $file_dir = public_path('/files/member/');
                if (!File::exists($file_dir)) {
                    File::makeDirectory($file_dir, $mode = 0777, true, true);
                }

                $image = $request->file('member_image');
                $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));
                $image_name = "img_" . $slug . "_" . time() . "." . $image->getClientOriginalExtension();
                $image->move($file_dir, $image_name);

                $host = env('APP_URL');

                $image_name = $host . '/public/files/member/' . $image_name;
            } else {
                $image_name = NULL;
            }

            $data = MemberModel::where('member_id', $id)->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            if ($request->member_password != $data->member_password) {
                $password = Hash::make($params['member_password']);
            } else {
                $password = $data->member_password;
            }

            $data->period_id = $params['period_id'];
            $data->member_nim = $params['member_nim'];
            $data->member_name = $params['member_name'];
            $data->member_status = $params['member_status'];
            $data->member_birthdate = $params['member_birthdate'];
            $data->member_address = $params['member_address'];
            $data->member_phone = $params['member_phone'];
            $data->member_email = $params['member_email'];
            $data->member_password = $password;
            $data->member_image_url = $image_name;
            $data->updated_at = now();
            $data->structure_id = $params['structure_id'];
            $data->division_id = $params['division_id'];
            $data->save();

            // UPDATE AUTO REGISTER
            $user = User::where('nim', $data->member_nim)->first();
            if (empty($user)) {
                $user = User::create([
                    'nim' => $data->member_nim,
                    'member_id' => $data->member_id,
                    'name' => $data->member_name,
                    'email' => $data->member_email,
                    'password' => $password,
                ]);
            } else {
                $user->nim = $data->member_nim;
                $user->member_id = $data->member_id;
                $user->name = $data->member_name;
                $user->email = $data->member_email;
                $user->password = $password;
                $user->save();
            };

            return response()->json(APIFormatter::createApi(200, 'Success', $data));
        } catch (\Exception $e) {
            return response()->json(APIFormatter::createApi(400, $e->getMessage()));
        }
    }

    public function destroy($id)
    {
        try {
            $data = MemberModel::where('member_id', $id)->first();
            $user = User::where('member_id', $id)->first();

            if (is_null($data)) {
                return response()->json(APIFormatter::createApi(404, 'Data not found'));
            }

            if ($user) {
                $user->delete();
            }

            $data->delete();
            return response()->json(APIFormatter::createApi(200, 'Succes'));
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return response()->json(400, 'Cannot delete this data because it is used in another table');
            }
        } catch (\Exception $e) {
            return response()->json(APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage()));
        }
    }

    public function get_member()
    {
        $member = Auth::user();
        $period = PeriodModel::where('period_status', '1')->orderby('period_name', 'desc')->first();
        if (Auth::user()->member_id) {
            $member = MemberModel::where('member_email', Auth::user()->email)->first();
        } else {
            $member = [
                'member_id' => null,
                'period_id' => $period['period_id'],
                "member_nim" => $member['nim'],
                "member_name" => $member['name'],
                "member_email" => $member['email'],
                "member_status" => "active",
                "member_address" => null,
                "member_phone" => null,
                "member_image_url" => null,
                "member_birthdate" => null,
            ];
        }
        return $member;
    }

    public function memberAuth()
    {
        $data = $this->get_member();
        $res = ['success' => true, 'user' => $data];
        return response()->json($res, 200);
    }
}
