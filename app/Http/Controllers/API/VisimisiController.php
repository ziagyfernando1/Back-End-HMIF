<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\VisimisiModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VisimisiController extends Controller
{
    public function index()
    {
        try {
            $data = VisimisiModel::select('*')
                ->leftjoin('period', 'visimisi.period_id', '=', 'period.period_id')
                ->get();
            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function indexActive()
    {
        try {
            $data = VisimisiModel::select('*')
                ->leftjoin('period', 'visimisi.period_id', '=', 'period.period_id')
                ->where('period_status', '1')
                ->first();
            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make(
                $params,
                [
                    'period_id' => 'required',
                    'visimisi_visi' => 'required',
                    'visimisi_misi' => 'required'
                ],
                [
                    'period_id.required' => 'Period id is required',
                    'visimisi_visi.required' => 'Visi is required',
                    'visimisi_misi.required' => 'Misi is required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = VisimisiModel::create($params);
            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function show($id)
    {
        try {
            $data = VisimisiModel::select('*')
                ->leftjoin('period', 'visimisi.period_id', '=', 'period.period_id')
                ->where('visimisi_id', $id)
                ->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Not Found'));
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
                    'visimisi_visi' => 'required',
                    'visimisi_misi' => 'required'
                ],
                [
                    'period_id.required' => 'Period id is required',
                    'visimisi_visi.required' => 'Visi is required',
                    'visimisi_misi.required' => 'Misi is required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = VisimisiModel::where('visimisi_id', $id)->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Not found'));
            }

            $data->update($params);
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
            $data = VisimisiModel::where('visimisi_id', $id)->first();

            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'not found'));
            }

            $data->delete();
            $response = APIFormatter::createApi(200, 'Success', $data);
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
