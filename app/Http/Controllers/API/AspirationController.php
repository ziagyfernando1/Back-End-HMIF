<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\PeriodModel;
use App\Models\StudentModel;
use App\Models\AspirationModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AspirationController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->search;
        $sort       = $request->sort ?? "created_at";
        $sort_type  = $request->sort_type ?? "desc";

        $query       = AspirationModel::select('aspiration.*', 'student.student_name')->leftjoin('student', 'student.student_id', '=', 'aspiration.student_id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('aspiration_class', 'LIKE', '%' . $search . '%');
                $q->orWhere('aspiration_message', 'LIKE', '%' . $search . '%');
                $q->orWhere('aspiration_period', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy($sort, $sort_type)->get();

        $response   = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make(
                $params,
                [
                    'aspiration_message' => 'required',
                    'student_npm' => 'required',
                ],
                [
                    'aspiration_message.required' => 'Aspiration message is required',
                    'student_npm.required' => 'NIM is required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $period = PeriodModel::where('period_status', '1')
                ->orderby('period_name', 'desc')
                ->first();

            $student = $this->getStudent($params);
            $student = $student->original;

            if ($student['meta']['code'] != 200) {
                return ApiFormatter::createApi(400, $student['meta']['message']['Message']);
            };

            $aspiData = [
                'aspiration_class'   => $params['aspiration_class'] ?? null,
                'aspiration_subject' => $params['aspiration_subject'] ?? null,
                'aspiration_period'  => $period['period_id'],
                'aspiration_message' => $params['aspiration_message'],
                'student_id'         => $student['data'],
            ];

            $data = AspirationModel::create($aspiData);
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
            $data = AspirationModel::where('aspiration_id', $id)->first();

            if (is_null($data)) {
                return APIFormatter::createApi(404, 'Data Not Found.');
            }

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
            $data = AspirationModel::where('aspiration_id', $id)->first();

            if (is_null($data)) {
                return APIFormatter::createApi(404, 'Data Not Found.');
            }

            $data->delete();
            $response = APIFormatter::createApi(200, 'Delete Data Success');
            return response()->json($response);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                $response = APIFormatter::createApi(400, 'Cannot delete this data because it is used in another table');
                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function getStudent($params)
    {
        $Checker = new AuthController();
        $student = $Checker->nimChecking($params['student_npm']);
        $student = $student->original;

        if ($student['meta']['code'] != 200) {
            return ApiFormatter::createApi(400, $student['meta']['message']['Message']);
        };

        $studentData = [
            'student_npm'        => $student['data']['Npm'],
            'student_name'       => $student['data']['Nama'],
        ];

        $student    = StudentModel::where('student_npm', $params['student_npm'])->first();
        $studentId  = null;

        if ($student) {
            $studentId = $student->student_id;
        } else {
            $newStudent = StudentModel::create($studentData);
            $studentId  = $newStudent->student_id;
        }

        return ApiFormatter::createApi(200, 'NIM is valid', $studentId);
    }
}
