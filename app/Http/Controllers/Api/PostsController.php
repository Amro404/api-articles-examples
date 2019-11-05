<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use App\Http\Resources\PostResource;
use App\Http\Requests\Posts\CreatePostRequest;


class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware("jwt.verify")->only(["index", "update", "store", "destroy"]);
    }

	use ApiResponseTrait;

    public function index()
    {

    	$posts = PostResource::collection(Post::paginate($this->paginateNumber()));

		return $this->apiResponse($posts);

    }

    public function show($id)
    {
    	$post = Post::find($id);

    	if(!$post) {
    		return $this->apiResponse(null, "This post is not found", 404);
    	}

    	return $this->apiResponse(new PostResource($post));
    }

    public function store(Request $request)
    {
    	$validation = $this->validation($request);

    	if ($validation instanceof Response) {
    		return $validation;
    	}	

    	$post = Post::create($request->all());

		if(!$post) {
    		return $this->unKnownError();
    	}

    	return $this->createdResponse(new PostResource($post));

    }

    public function update($id, Request $request)
    {
    	$validation = $this->validation($request);

    	if ($validation instanceof Response) {
    		return $validation;
    	}

    	$post = Post::find($id);

		if(!$post) {
    		return $this->unKnownError();
    	}

    	$post->update($request->all());

    	return $this->createdResponse(new PostResource($post));

    }


    public function validation($request)
    {
    	return $this->apiValidation($request, [
    		'title' => 'required|min:3|max:191',
            'body' => 'required|min:10',
    	]);

    }

    public function destroy($id)
    {
    	$post = Post::findOrFail($id);

    	if(!$post) {
    		return $this->unKnownError();
    	}

    	$post->delete();

    	return $this->createdResponse(new PostResource($post));
    }

}
