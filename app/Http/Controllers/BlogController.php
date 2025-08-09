<?php

namespace App\Http\Controllers;

use App\Http\Requests\Blog\ListRequest;
use App\Http\Requests\Blog\StoreRequest;
use App\Http\Requests\Blog\UpdateRequest;
use App\Http\Resources\Blog\BlogCollection;
use App\Http\Resources\Blog\BlogResource;
use App\Models\Blog;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs (public).
     */
    public function index(ListRequest $request)
    {
        $validated = $request->validated();

        $query = Blog::with('user:id,name');

        // Apply search scope
        $query->search($request->search);

        // Apply ordering scope
        switch ($request->order) {
            case 'oldest':
                $query->oldest();
                break;
            case 'most_liked':
                $query->mostLiked();
                break;
            case 'alphabetical':
                $query->alphabetical();
                break;
            default:
                $query->latest();
                break;
        }

        $blogs = $query->paginate($request->per_page);

        return new BlogCollection([
            'blogs' => $blogs,
            'message' => 'Blogs retrieved successfully'
        ]);
    }

    /**
     * Display the specified blog (public).
     */
    public function show($slug)
    {
        $blog = Blog::with('user:id,name')->where('slug', $slug)->first();

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        return new BlogResource([
            'blog' => $blog,
            'message' => 'Blog retrieved successfully'
        ]);
    }

    /**
     * Store a newly created blog (authenticated users only).
     */
    public function store(StoreRequest $request)
    {
        // Generate unique slug
        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;

        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $blogData = [
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ];

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $coverImagePath = FileUploadService::uploadFile(
                $request->file('cover_image'),
                'blogs/covers'
            );
            $blogData['cover_image'] = $coverImagePath;
        }

        $blog = Blog::create($blogData);
        $blog->load('user:id,name');

        return new BlogResource([
            'blog' => $blog,
            'message' => 'Blog created successfully'
        ]);
    }

    /**
     * Update the specified blog (authenticated users can only update their own blogs).
     */
    public function update(UpdateRequest $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        // Check if user owns the blog
        if ($blog->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. You can only update your own blogs.'
            ], 403);
        }

        $updateData = [];

        if ($request->has('title')) {
            $updateData['title'] = $request->title;
            // Generate new slug if title changed
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $counter = 1;

            while (Blog::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $updateData['slug'] = $slug;
        }

        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $coverImagePath = FileUploadService::uploadFile(
                $request->file('cover_image'),
                'blogs/covers',
                $blog->cover_image // Delete old file
            );
            $updateData['cover_image'] = $coverImagePath;
        }

        $blog->update($updateData);
        $blog->load('user:id,name');

        return new BlogResource([
            'blog' => $blog,
            'message' => 'Blog updated successfully'
        ]);
    }

    /**
     * Get authenticated user's blogs.
     */
    public function myBlogs(ListRequest $request)
    {
        $validated = $request->validated();

        $query = $request->user()->blogs()->with('user:id,name');

        // Apply search scope
        $query->search($validated['search']);

        // Apply ordering scope
        switch ($validated['order']) {
            case 'oldest':
                $query->oldest();
                break;
            case 'most_liked':
                $query->mostLiked();
                break;
            case 'alphabetical':
                $query->alphabetical();
                break;
            default:
                $query->latest();
                break;
        }

        $blogs = $query->paginate($validated['per_page']);

        return new BlogCollection([
            'blogs' => $blogs,
            'message' => 'Your blogs retrieved successfully'
        ]);
    }
}
