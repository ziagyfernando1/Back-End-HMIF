<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PeriodModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeriodController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'created_at';
        $sort_type = $request->sort_type ?? 'desc';

        $query = PeriodModel::select('*');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('period_name', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy($sort, $sort_type)->get();

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
                    'period_name' => 'required',
                    'period_status' => 'required',
                ],
                [
                    'period_name.required' => 'Period name is required',
                    'period_status.required' => 'Period status is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            if (PeriodModel::where('period_name', $params['period_name'])->exists()) {
                $response = APIFormatter::createApi(400, 'Period name already exists');
                return response()->json($response);
            }

            $data = PeriodModel::create([
                'period_name' => $params['period_name'],
                'period_status' => $params['period_status'],
            ]);

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
            $data = PeriodModel::where('period_id', $id)->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Period not found'));
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
                    'period_name' => 'required',
                    'period_status' => 'required',
                ],
                [
                    'period_name.required' => 'Period name is required',
                    'period_status.required' => 'Period status is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = PeriodModel::findorfail($id);

            if ($params['period_status'] != $data['period_status'] && $params['period_status'] == 1) {
                $otherPeriods = PeriodModel::where('period_status', '1')
                    ->where('period_id', '!=', $id)
                    ->get();

                foreach ($otherPeriods as $period) {
                    $period->period_status = '0';
                    $period->save();
                }
            }

            $data->update([
                'period_name' => $params['period_name'],
                'period_status' => $params['period_status'],
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
            $data = PeriodModel::where('period_id', $id)->first();

            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Period not found'));
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
