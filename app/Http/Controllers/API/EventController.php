<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\EventModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'event.created_at';
        $sort_type = $request->sort_type ?? 'desc';

        $query = EventModel::select('*')
            ->leftjoin('program', 'event.program_id', '=', 'program.program_id')
            ->leftjoin('staff', 'staff.staff_id', '=', 'event.staff_id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('event_name', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy($sort, $sort_type)->get();

        $response   = APIFormatter::createApi(200, 'succes', $data);
        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make(
                $params,
                [
                    'program_id' => 'required',
                    'staff_id' => 'required',
                    'event_name' => 'required',
                    'event_date' => 'required',
                ],
                [
                    'program_id.required' => 'Program ID is required',
                    'staff_id.required' => 'Staff ID is required',
                    'event_name.required' => 'Event Name is required',
                    'event_date.required' => 'Event Date is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = EventModel::create([
                'program_id' => $params['program_id'],
                'staff_id' => $params['staff_id'],
                'event_name' => $params['event_name'],
                'event_date' => $params['event_date'],
                'event_status' => $params['event_status']
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
            $data = EventModel::where('event_id', $id)->first();
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
                    'program_id' => 'required',
                    'staff_id' => 'required',
                    'event_name' => 'required',
                    'event_date' => 'required',
                ],
                [
                    'program_id.required' => 'Program ID is required',
                    'staff_id.required' => 'Staff ID is required',
                    'event_name.required' => 'Event Name is required',
                    'event_date.required' => 'Event Date is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = EventModel::where('event_id', $id)->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->update([
                'program_id' => $params['program_id'],
                'staff_id' => $params['staff_id'],
                'event_name' => $params['event_name'],
                'event_date' => $params['event_date'],
                'event_status' => $params['event_status'],
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
            $data = EventModel::where('event_id', $id)->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Data not found'));
            }

            $data->ProgramModel()->delete();
            $data->staff()->delete();

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
