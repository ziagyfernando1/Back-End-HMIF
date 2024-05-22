<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SubjectModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
    public function index()
    {
        $data = SubjectModel::select('*')->get();
        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, 
                [
       
                    'subject_name' => 'required',
                    'subject_code' => 'required',
                ],
                [
                    
                    'subject_name.required' => 'subject name is required',
                    'subject_code.required' => 'subject code is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = SubjectModel::create([
                'subject_code' => $params['subject_code'],
                'subject_name' => $params['subject_name'],
                
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
            $data = SubjectModel::where('subject_id', $id)->first();
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
                    
                    'subject_name' => 'required',
                    'subject_code' => 'required',
                ],
                [
                    
                    'subject_name.required' => 'subject name is required',
                    'subject_code.required' => 'subject code is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = SubjectModel::where('subject_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            
            $data->update([
                'subject_code' => $params['subject_code'],
                'subject_name' => $params['subject_name'],
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
            $data = SubjectModel::where('subject_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            
            $data->delete();
            $response = APIFormatter::createApi(200, 'Succes');
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
}
