<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\StructureModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StructureController extends Controller
{
    public function index()
    {
        $data = StructureModel::select('*') ->leftjoin ('period','structure.period_id','=','period.period_id') -> get();
        $response = APIFormatter:: createApi(200,'Success',$data);
        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $params =$request->all();
            $validator = Validator::make($params, 
                [
                    'period_id' => 'required',
                    'structure_name' => 'required',
                    'structure_level' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'structure_name.required' => 'Structure name is required',
                    'structure_level.required' => 'Structure level is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400,'Validation Error',$validator->errors()->all());
                return response()->json($response);
            }

            $data = StructureModel::create([
                'period_id'=> $params['period_id'],
                'structure_name' => $params['structure_name'],
                'structure_level'=> $params['structure_level']
            ]);

            $response = APIFormatter::createApi(200,'Success',$data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response =APIFormatter::createApi(500,'Internal Server Error', $e->getMessage() );
            return response()->json($response);

        }
    }

    public function show($id)
    {
        try { 
            $data = StructureModel::where('structure_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            $response = APIFormatter::createAPI(200,'succes',$data);
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
                    'structure_name' => 'required',
                    'structure_level' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'structure_name.required' => 'Structure name is required',
                    'structure_level.required' => 'Structure level is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400,'Validation Error',$validator->errors()->all());
                return response()->json($response);
            }

            $data = StructureModel::where('structure_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            
            $data->update([
                'period_id'       => $params['period_id'],
                'structure_name'  => $params['structure_name'],
                'structure_level' => $params['structure_level'],
                'updated_at'      => now()
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
            $data = StructureModel::where('structure_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->delete();
            $response = APIFormatter::createAPI(200,'succes');
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
