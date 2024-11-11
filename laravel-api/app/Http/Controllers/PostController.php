<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator as phument;

class PostController extends Controller
{
    public function index(){

        $posts = Post::orderBy('id','DESC')->get();
        $data = [];
        foreach ($posts as $post){
            $data[] = [
                "id"    => $post->id,
                "title" => $post->title,
                "des"   => $post->des,
                "image" => ($post->image != null) ? asset('images/'.$post->image) : 'empty image',
            ];
        }

        return response()->json([
            'status' => true,
            'message' => "Post was successfully selected",
            'posts'   => $data
        ],200);
    }


    public function store(Request $request){
        $validator = phument::make($request->all(),[
            "title" => 'required|min:5',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Please configure the validation',
                'errors'  =>  $validator->errors(),
            ],500);
        }


        //Dependency injection
        $post = new Post();
        $post->title = $request->title;
        $post->des   = $request->des;

        if($request->file("image") != null){
            $file = $request->file("image");
            $image = rand(000,9999999) .'.' . $file->getClientOriginalExtension();
            //34564554.jpg

            //move image to the folder
            $file->move(public_path("images"),$image);


            //save image to the database
            $post->image = $image;


        }


        $post->save();

        return response([
            'status' => true,
            'message' => 'Post saved successfully',
            'post'  => $post,
        ],201);

    }

    public function edit(string $id){
        $post = Post::find($id);

        if(!$post){
            return response([
                'status' => false,
                'message' => 'Post not found',
            ],404);
        }

        //convert 
        $responePost = [
            'id'    => $post->id,
            'title' => $post->title,
            'des'   => $post->des,
            'image' => ($post->image != null) ? asset('images/'.$post->image) : 'empty image',
        ];

        return response([
            'status' => true,
            'message' => 'Post is found',
            'post' => $responePost,
        ],200);
    }

    public function update(Request $request,string $id){

        $validator = phument::make($request->all(),[
            'title' => 'required',
        ]);

        $post = Post::find($id);



        if(!$post){
            return response([
                'status' => false,
                'message' => 'Post not found',
            ],404);
        }


        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Please configure the validation',
                'errors'  =>  $validator->errors(),
            ],500);
        }


        $post->title = $request->title;
        $post->des   = $request->des;

        if($request->file('image') != null){
            $file = $request->file('image');
            $image = rand(000,9999999).'.' . $file->getClientOriginalExtension();

            //move new image to folder 
            $file->move(public_path('images'),$image);

            //delete old image
            if($post->image != null){
                $imageDir = public_path("images/".$post->image);
                if(File::exists($imageDir)){
                    File::delete($imageDir);
                }
            }

           
        }else{
            $image = $post->image;
        }
        //save new image to the database
        $post->image = $image;

        $post->update();

        return response([
            'status' => true,
            'message' => 'Post updated successfully',
            'post'  => $post,
        ],201);

    }

    public function delete($id){

        $post = Post::find($id);


        if(!$post){
            return response([
                'status' => false,
                'message' => 'Post not found',
            ],404);
        }

        if($post->image != null){
            $imageDir = public_path("images/".$post->image);
            if(File::exists($imageDir)){
                File::delete($imageDir);
            }
        }

        $post->delete();

        return response([
            'status' => true,
            'message' => 'Post deleted successfully',
        ]);


    }




}
