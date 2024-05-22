<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\RegistrationModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function index()
    {
        $data =  RegistrationModel::all();
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
                    'registration_name' => 'required',  
                ],
                [
                    'event_id.required' => 'Event ID is required',
                    'registration.required' => 'registration is required',    
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = RegistrationModel::create([
                'event_id'          => $params['event_id'],
                'registration_name' => $params['registration_name'],
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
            $data = RegistrationModel::where('registration_id', $id)->first();
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
                    'event_id' => 'required',
                    'registration_name' => 'required',
                ],
                [
                    'event_id.required' => 'event ID is required',
                    'registration_name.required' => 'registration name is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = RegistrationModel::where('registration_id', $id)->first();
            if(is_null($data)){
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }
            
            $data->update([
                'event_id'          => $params['event_id'],
                'registration_name' => $params['registration_name'],
                'updated_at'        => now()
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
            $data = RegistrationModel::where('registration_id', $id)->first();
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
