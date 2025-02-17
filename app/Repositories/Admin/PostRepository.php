<?php

namespace App\Repositories\Admin;

use App\FileKit\Upload;
use App\Http\Requests\Admin\AddPostRequest;
use App\Http\Requests\Admin\EditPostRequest;
use App\Interfaces\Repositories\Admin\DBPostsInterface;
use App\Models\Field;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class PostRepository implements DBPostsInterface
{

    /**
     * @inheritDoc
     */
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $admin = Auth::user();
        return view('admin.posts.index', [
            'posts' => $admin->posts,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function create(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('admin.posts.add', [
            'fields' => Field::all(),
        ]);
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function store(AddPostRequest $request): RedirectResponse
    {
        $post = Post::create(array_merge($request->all(), ['admin_id' => Auth::id()]));

        Upload::uploadFile('layout_img', Post::class, "$post->id");

        if ($post) {
            return Redirect::route('posts.index')->with('success-add-post', 'success added post  ' . $post->title);
        }
        return Redirect::route('posts.index')->with('success-fail-post', 'fail added post  ' . $post->title);
    }

    /**
     * @inheritDoc
     */
    public function show(string $id): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $post = Post::find($id);

        return view('admin.posts.post', [
            'post' => optional($post)->is_active ? $post : null,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function edit(string $id): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('admin.posts.edit', [
            'post' => Post::find($id),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function update(EditPostRequest $request, string $id): RedirectResponse
    {

        $post = Post::findOrFail($id);
        $post->update($request->validated());

        if ($post->save()) {
            return Redirect::route('posts.index')->with('success-edit-post', 'success edit post  ' . $post->title);
        }
        return Redirect::route('posts.index')->with('fail-edit-post', 'success edit post  ' . $post->title);
    }

    /**
     * @inheritDoc
     */
    public function destroy(string $id): RedirectResponse
    {
        $post = Post::find($id);

        if (is_null($post)) return Redirect::route('posts.index')->with('fail-delete-post', 'the founded this post in our data');


        if ($post->delete()) {
            foreach ($post->images as $index => $image) {
                Upload::delete($image);
            }
            return Redirect::route('posts.index')->with('success-delete-post', 'success delete post  ' . $post->title);
        }
        return Redirect::route('posts.index')->with('fail-delete-post', 'fail delete post  ' . $post->title);
    }
}
