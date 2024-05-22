<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\PartnershipModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PartnershipController extends Controller
{
    public function index()
    {
        $data = PartnershipModel::select('*')->get();
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
                    'period_id' => 'required',
                    'management_id' => 'required',
                    'partnership_name' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'management_id.required' => 'Management ID is required',
                    'partnership_id.required' => 'Partnership ID is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = PartnershipModel::create([
                'period_id' => $params['period_id'],
                'management_id' => $params['management_id'],
                'partnership_name' => $params['partnership_name'],
                'partnership_phone' => $params['partnership_phone'],
                'partnership_email' => $params['partnership_email']
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
            $data = PartnershipModel::where('partnership_id', $id)->first();
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
                    'period_id' => 'required',
                    'management_id' => 'required',
                    'partnership_name' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'management_id.required' => 'Management ID is required',
                    'partnership_id.required' => 'Partnership ID is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = PartnershipModel::where('partnership_id', $id)->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->update([
                'period_id' => $params['period_id'],
                'management_id' => $params['management_id'],
                'partnership_name' => $params['partnership_name'],
                'partnership_phone' => $params['partnership_phone'],
                'partnership_email' => $params['partnership_email'],
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
            $data = PartnershipModel::where('partnership_id', $id)->first();
            if (is_null($data)) {
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
