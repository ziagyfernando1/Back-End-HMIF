<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ArticleModel;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use File;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->search;
        $sort       = $request->sort ?? "created_at";
        $sort_type  = $request->sort_type ?? "desc";

        $query = ArticleModel::select('article.*', 'category.category_name', 'member.member_name', 'member.member_nim')
            ->leftjoin('category', 'article.category_id', '=', 'category.category_id')
            ->leftjoin('member', 'article.member_id', '=', 'member.member_id')
            ->with('member', 'member.period');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('article_title', 'LIKE', '%' . $search . '%');
                $q->orWhere('article_content', 'LIKE', '%' . $search . '%');
                $q->orWhere('category_name', 'LIKE', '%' . $search . '%');
                $q->orWhere('member.member_nim', 'LIKE', '%' . $search . '%');
                $q->orWhere('member.member_name', 'LIKE', '%' . $search . '%');
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
                    'member_id' => 'required',
                    'category_id' => 'required',
                    'article_title' => 'required'
                ],
                [
                    'member_id.required' => 'Member id is required',
                    'category_id.required' => 'Category id is required',
                    'artice_title.required' => 'Article title is required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            if ($request->hasFile('article_image')) {
                $file_dir = public_path('/files/article/');
                if (!File::exists($file_dir)) {
                    File::makeDirectory($file_dir, $mode = 0777, true, true);
                }

                $image = $request->file('article_image');
                $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));
                $image_name = "img_" . $slug . "_" . time() . "." . $image->getClientOriginalExtension();
                $image->move($file_dir, $image_name);

                $host = env('APP_URL');

                $image_name = $host . '/public/files/article/' . $image_name;
            } else {
                $image_name = NULL;
            }

            $article_slugs = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));

            $data = ArticleModel::create([
                'member_id' => $params['member_id'],
                'category_id' => $params['category_id'],
                'article_title' => $params['article_title'],
                'article_content' => $params['article_content'],
                'article_image' => $image_name,
                'article_slug' => $article_slugs,
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
            $data = ArticleModel::where('article_id', $id)->first();
            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Article not found'));
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
                    'member_id' => 'required',
                    'category_id' => 'required',
                    'article_title' => 'required'
                ],
                [
                    'member_id.required' => 'member id is required',
                    'category_id.required' => 'Category id is required',
                    'artice_title.required' => 'Article title is required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            if ($request->hasFile('article_image')) {
                $file_dir = public_path('/files/article/');
                if (!File::exists($file_dir)) {
                    File::makeDirectory($file_dir, $mode = 0777, true, true);
                }

                $image = $request->file('article_image');
                $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));
                $image_name = "img_" . $slug . "_" . time() . "." . $image->getClientOriginalExtension();
                $image->move($file_dir, $image_name);

                $host = env('APP_URL');

                $image_name = $host . '/public/files/article/' . $image_name;
            } else {
                $image_name = NULL;
            }

            $article_slugs = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));

            $data = ArticleModel::where('article_id', $id)->first();

            $data->member_id = $params['member_id'];
            $data->category_id = $params['category_id'];
            $data->article_title = $params['article_title'];
            $data->article_content = $params['article_content'];
            $data->article_image = $image_name;
            $data->article_slug = $article_slugs;
            $data->updated_at = now();
            $data->save();

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
            $data = ArticleModel::where('article_id', $id)->first();

            if (is_null($data)) {
                return response()->json(ApiFormatter::createApi(404, 'Article not found'));
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

    public function getArticles(Request $request)
    {
        $search     = $request->search;
        $sort       = $request->sort ?? "created_at";
        $sort_type  = $request->sort_type ?? "desc";

        $query = ArticleModel::select('article.*', 'category.category_name', 'member.member_name', 'member.member_nim')
            ->leftjoin('category', 'article.category_id', '=', 'category.category_id')
            ->leftjoin('member', 'article.member_id', '=', 'member.member_id')
            ->leftJoin('period', 'member.period_id', '=', 'period.period_id')
            ->where('category.category_name', '=', 'article') // Filter data only article
            ->with('category', 'member', 'member.period'); // Append the "category", "member", and "period" objects

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('article_title', 'LIKE', '%' . $search . '%');
                $q->orWhere('article_content', 'LIKE', '%' . $search . '%');
                $q->orWhere('category_name', 'LIKE', '%' . $search . '%');
                $q->orWhere('member.member_nim', 'LIKE', '%' . $search . '%');
                $q->orWhere('member.member_name', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy($sort, $sort_type)->get();

        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    public function getNews(Request $request)
    {
        $search     = $request->search;
        $sort       = $request->sort ?? "created_at";
        $sort_type  = $request->sort_type ?? "desc";

        $query = ArticleModel::select('article.*', 'category.category_name', 'member.member_name', 'member.member_nim')
            ->leftjoin('category', 'article.category_id', '=', 'category.category_id')
            ->leftjoin('member', 'article.member_id', '=', 'member.member_id')
            ->leftJoin('period', 'member.period_id', '=', 'period.period_id')
            ->where('category.category_name', '=', 'news') // Filter data only news
            ->with('category', 'member', 'member.period'); // Append the "category", "member", and "period" objects

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('article_title', 'LIKE', '%' . $search . '%');
                $q->orWhere('article_content', 'LIKE', '%' . $search . '%');
                $q->orWhere('category_name', 'LIKE', '%' . $search . '%');
                $q->orWhere('member.member_nim', 'LIKE', '%' . $search . '%');
                $q->orWhere('member.member_name', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy($sort, $sort_type)->get();

        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }
}
