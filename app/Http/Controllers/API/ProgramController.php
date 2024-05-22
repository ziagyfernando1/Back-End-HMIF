<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProgramModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'program.program_date';
        $sort_type = $request->sort_type ?? 'desc';

        $query = ProgramModel::select('*')
            ->leftjoin('division', 'division.division_id', '=', 'program.division_id')
            ->leftjoin('period', 'division.period_id', '=', 'period.period_id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('program_name', 'LIKE', '%' . $search . '%');
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
                    'division_id' => 'required',
                    'program_name' => 'required',
                    'program_status' => 'required',
                ],
                [
                    'division_id.required' => 'division ID is required',
                    'program_name.required' => 'Program Name is required',
                    'program_status.required' => 'Program Status is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = ProgramModel::create([
                'division_id' => $params['division_id'],
                'program_name' => $params['program_name'],
                'program_status' => $params['program_status'],
                'program_desc' => $params['program_description'] ?? null,
                'program_date' => $params['program_date'] ?? null,
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
            $data = ProgramModel::where('program_id', $id)->first();
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
                    'division_id' => 'required',
                    'program_name' => 'required',
                    'program_status' => 'required',
                ],
                [
                    'division_id.required' => 'division ID is required',
                    'program_name.required' => 'Program Name is required',
                    'program_status.required' => 'Program Status is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = ProgramModel::where('program_id', $id)->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->update([
                'division_id' => $params['division_id'],
                'program_name' => $params['program_name'],
                'program_status' => $params['program_status'],
                'program_desc' => $params['program_description'],
                'program_date' => $params['program_date'],
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
            $data = ProgramModel::where('program_id', $id)->first();
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
