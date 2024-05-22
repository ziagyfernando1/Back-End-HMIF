<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\RecruitmentModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RecruitmentController extends Controller
{
    public function index()
    {
        $data = RecruitmentModel::select('*')->leftjoin('event','event.event_id','=','recruitment.event_id')->get();
        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, 
                [
                    'event_id' => 'required',
                    'recruitment_name' => 'required',
                ],
                [
                    'event_id.required' => 'Event ID is required',
                    'recruitment_name.required' => 'Recruitment Name ID is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = RecruitmentModel::create([
                'event_id' => $params['event_id'],
                'recruitment_name' => $params['recruitment_name']
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
            $data = RecruitmentModel::where('recruitment_id', $id)->first();

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

            $validator = Validator::make($params, [
                'event_id' => 'required',
                'recruitment_name' => 'required',
            ],
            [
                'event_id.required' => 'Event ID is required',
                'recruitment_name.required' => 'Recruitment Name ID is required',
            ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = RecruitmentModel::where('recruitment_id', $id)->first();

            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            
            $data->update();

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
            $data = RecruitmentModel::where('recruitment_id', $id)->first();

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
