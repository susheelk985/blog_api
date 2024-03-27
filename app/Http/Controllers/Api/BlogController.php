<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Validator;
use Auth;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'content' => 'required',
                'date' => 'required',
                'tags' => 'required',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $blog = Blog::create([
                'name' => $request->name,
                'content' => $request->content,
                'date' => $request->date,
                'tags' => json_encode($request->tags),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Blog Created Successfully',
                'data' => $blog
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $requestArray = (array)$request;
        try {
            if(!$request->all())
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Nothing to update!.',

                ], 200);
            }

            $blog = Blog::where('user_id',Auth::user()->id)->where('id',$id)->first();
            if($blog)
            {
                $blog->name = $request->name;
                $blog->content = $request->content;
                $blog->tags = json_encode($request->tags);
                $blog->save();
            }else{
                return response()->json([
                    'status' => true,
                    'message' => 'No blogs found!',
                ], 200);
            }



            return response()->json([
                'status' => true,
                'message' => 'Blog updated Successfully',
                'data' => $blog
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $blog = Blog::where('user_id',Auth::user()->id)->where('id',$id)->first();
            if(!$blog)
            {
                return response()->json([
                    'status' => true,
                    'message' => 'No blog found or already deleted!.',

                ], 200);
            }

            $blog->delete();

            return response()->json([
                'status' => true,
                'message' => 'Blog deleted Successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

    public function filter(Request $request)
    {
        try {

            // return $request->date;
            if($request->all())
            {
                $blogs = Blog::orwhere('user_id',$request->user_id)
                     ->orwhereJsonContains('tags',($request->tag))
                     ->orwhereDate('date',$request->date)
                     ->limit($request->limit)
                     ->get();
            }else{
                $blogs = Blog::get();
            }

            return response()->json([
                'status' => true,
                'message' => 'Blogs',
                'data' => $blogs
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
