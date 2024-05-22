<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SelectionModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;

class SelectionController extends Controller
{
    public function index()
    {
        $data =  SelectionModel::all();
        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, 
                [
                    'recruitment_id' => 'required',
                    'selection_name' => 'required',
                ],
                [
                    'recruitment_id.required' => 'Recruitment ID is required',
                    'selection_name.required' => 'selection name ID is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = SelectionModel::create([
                'recruitment_id' => $params['recruitment_id'],
                'selection_name' => $params['selection_name'],
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
            $data = SelectionModel::where('selection_id', $id)->first();
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
                    'recruitment_id' => 'required',
                    'selection_name' => 'required',
                ],
                [
                    'recruitment_id.required' => ' Recruitment ID is required',
                    'selection_name.required' => 'selection name is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = SelectionModel::where('selection_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            
            $data->update([
                'recruitment_id' => $params['recruitment_id'],
                'selection_name' => $params['selection_name'],
                'updated_at'     => now()
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
            $data = SelectionModel::where('selection_id', $id)->first();
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
