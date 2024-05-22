<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\DivisionModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProgramModel;
use Illuminate\Support\Facades\Validator;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort   = $request->sort ?? "created_at";
        $sort_type = $request->sort_type ?? "desc";
        $is_active = $request->period_status;

        $query = DivisionModel::select('division.*', 'period.period_name', 'period.period_status')->leftjoin('period', 'division.period_id', '=', 'period.period_id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('division_name', 'LIKE', '%' . $search . '%');
            });
        }

        if (!empty($is_active)) {
            $query->where('period_status', '1');
        }

        # OLD PROCESSING -> Using it for now
        $data = $query->orderBy($sort, $sort_type)->get();

        foreach ($data as $item) {
            $item['division_name'] = $item['division_name'] . ' ( ' . $item['period_name'] . ' )';
        }

        # ADVANCED PROCESSING -> DISABLE for now
        // $data = [];

        // $query->orderBy($sort, $sort_type)->chunk(1000, function ($chunkData) use (&$data) {
        //     foreach ($chunkData as $item) {
        //         $item['division_name'] = $item['division_name'] . ' ( ' . $item['period_name'] . ' )';
        //         $data[] = $item;
        //     }
        // });

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
                    'division_name' => 'required',
                    'division_description' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'division_name.required' => 'Division Name is required',
                    'division_description.required' => 'Division Description is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $divisionData = [
                'period_id'             => $params['period_id'],
                'division_name'         => $params['division_name'],
                'division_description'  => $params['division_description'],
                'division_function'     => $params['division_function'] ?? null,
                'division_icon'         => $params['division_icon'] ?? null,
            ];

            $data = DivisionModel::create($divisionData);

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
            $data = DivisionModel::where('division_id', $id)->first();
            if (is_null($data)) {
                return APIFormatter::createApi(404, 'Data not found');
            }

            $programs = ProgramModel::where('division_id', $id)
                ->orderby('program_date', 'desc')
                ->get();
            $data['programs'] = $programs;

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
                    'division_name' => 'required',
                    'division_description' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'division_name.required' => 'Division Name is required',
                    'division_description.required' => 'Division Description is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = DivisionModel::where('division_id', $id)->first();

            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Division not found'));
            }

            $divisionData = [
                'period_id'             => $params['period_id'],
                'division_name'         => $params['division_name'],
                'division_description'  => $params['division_description'],
                'division_function'     => $params['division_function'] ?? null,
                'division_icon'         => $params['division_icon'] ?? null,
                'updated_at'            => now(),
            ];

            $data->update($divisionData);

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
            $data = DivisionModel::where('division_id', $id)->first();

            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Division not found'));
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
