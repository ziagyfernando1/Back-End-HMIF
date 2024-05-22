<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Event_PartnershipModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class Event_PartnershipController extends Controller
{
    public function index()
    {

        $data =  Event_PartnershipModel::get();
        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make(
                $params,
                [
                    'event_id' => 'required',
                    'partnership_id' => 'required',
                    'event_partnership_detail' => 'required'

                ],
                [
                    'event_id.required' => 'Event ID is required',
                    'partnership_id.required' => 'Partnership ID is required',
                    'event_partnership_detail' => 'Event partnership detail required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = Event_PartnershipModel::create([
                'event_id' => $params['event_id'],
                'partnership_id' => $params['partnership_id'],
                'event_partnership_detail' => $params['event_partnership_detail']
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
            $data = Event_PartnershipModel::where('event_partnership_id', $id)->first();

            if (is_null($data)) {
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

            $validator = Validator::make(
                $params,
                [
                    'event_id' => 'required',
                    'partnership_id' => 'required',
                    'event_partnership_detail' => 'required'


                ],
                [
                    'event_id.required' => 'Program ID is required',
                    'partnership_id.required' => 'Staff ID is required',
                    'event_partnership_detail' => 'event partnership required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = Event_PartnershipModel::where('event_partnership_id', $id)->first();

            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->update([
                'event_id' => $params['event_id'],
                'partnership_id' => $params['partnership_id'],
                'event_partnership_detail' => $params['event_partnership_detail'],
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
            $data = Event_PartnershipModel::where('event_partnership_id', $id)->first();

            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->delete();

            $response = APIFormatter::createApi(200, 'Success');
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
