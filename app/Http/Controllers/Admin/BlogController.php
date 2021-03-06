<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;

use App\Category;
use App\Post;

class BlogController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function add()
    {
        $categories = Category::orderBy('name')->get();

        return view('administration.blog.add', [
            'categories' => $categories
        ]);
    }

    public function insert(Request $request)
    {
        $rules = [
            'title'   => 'required|min:3',
            'slug'    => 'required|unique:posts,slug',
            'content' => 'required|min:12'
        ];

        $this->validate($request, $rules, ['content.min' => 'The content must be filled in']);

        $post = Post::create([
            'title'   => $request->input('title'),
            'slug'    => $request->input('slug'),
            'content' => $request->input('content'),
        ]);

        // save the categories
        if($request->has('categories'))
        {
            $post->categories()->sync($request->input('categories'));
        }

        return Redirect::route('administration.index');
    }

    public function delete($id)
    {
        $post = Post::where('id', $id)->first();

        if(empty($post))
        {
            return Redirect::route('administration.index');
        }

        $post->delete();

        return Redirect::route('administration.index');
    }

    public function publish($id)
    {
        $post = Post::where('id', $id)->first();

        if(empty($post))
        {
            return Redirect::route('administration.index');
        }

        $post->published    = true;
        $post->published_at = date('Y-m-d H:i:s');
        $post->save();

        return Redirect::route('administration.index');
    }

    public function unpublish($id)
    {
        $post = Post::where('id', $id)->first();

        if(empty($post))
        {
            return Redirect::route('administration.index');
        }

        $post->published = false;
        $post->save();

        return Redirect::route('administration.index');
    }

    public function edit($id)
    {
        $post       = Post::where('id', $id)->first();
        $categories = Category::orderBy('name')->get();

        if(empty($post))
        {
            return Redirect::route('administration.index');
        }

        return view('administration.blog.edit', [
            'post'       => $post,
            'categories' => $categories
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'title'   => 'required|min:3',
            'content' => 'required|min:12'
        ];

        $this->validate($request, $rules, ['content.min' => 'The content must be filled in']);

        $post = Post::where('id', $request->input('id'))->first();

        if(empty($post))
        {
            return Redirect::route('administration.index');
        }

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();

        // save the categories
        if($request->has('categories'))
        {
            $post->categories()->sync($request->input('categories'));
        }

        return Redirect::route('administration.index');
    }

    /**
     * just a small API request to find the next slug
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function slug(Request $request)
    {
        if(! $request->has('slug'))
        {
            return response()->json([
                'valid' => false,
                'slug'  => ''
            ]);
        }

        // generate the default slug
        $slug = str_slug($request->input('slug'));

        // check to see if one already exists
        $lastSlug = Post::whereRaw("slug RLIKE '^". $slug ."(-[0-9]*)?$'")->latest('created_at')->value('slug');
        if($lastSlug)
        {
            // explode the parts
            $parts = explode('-', $lastSlug);
            $number = end($parts);

            $slug .= '-'. ($number + 1);
        }

        return response()->json([
                'valid' => true,
                'slug'  => $slug
        ]);
    }

}