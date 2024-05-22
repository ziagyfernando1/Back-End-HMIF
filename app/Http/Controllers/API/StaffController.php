<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\StaffModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function index()
    {
        $data = StaffModel::select('*')->leftjoin('period', 'staff.period_id', '=', 'period.period_id')->get();
        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, 
                [
                    'period_id' => 'required',
                    'member_id' => 'required',
                    'staff_level' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'member_id.required' => 'Member ID is required',
                    'staff_level.required' => 'Staff Level is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            if (StaffModel::where('member_id', $params['member_id'])->exists()) {
                $response = APIFormatter::createApi(400, 'member already exists');
                return response()->json($response);
            }

            $data = StaffModel::create([
                'period_id' => $params['period_id'],
                'member_id' => $params['member_id'],
                'staff_level' => $params['staff_level']
            ]);

            $response = APIFormatter::createApi(200, 'success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(400, $e->getMessage());
            return response()->json($response);
        }
    }

    public function show($id)
    {
        try {
            $data = StaffModel::where('staff_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, 
                [
                    'period_id' => 'required',
                    'member_id' => 'required',
                    'staff_level' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'member_id.required' => 'Member ID is required',
                    'staff_level.required' => 'Staff Level is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = StaffModel::where('staff_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->update([
                'period_id' => $params['period_id'],
                'member_id' => $params['member_id'],
                'staff_level' => $params['staff_level'],
                'updated_at' => now()
            ]);

            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function destroy($id)
    {
        try {
            $data = StaffModel::where('staff_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->delete();

            $response = APIFormatter::createApi(200, 'Success');
            return response()->json($response);
        } catch (\Illuminate\Database\QueryException $e) {
            if  ($e->getCode() == "23000") {
                $response = APIFormatter::createApi(400, 'Cannot delete this data because it is used in another table');
                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }
}