<?php

namespace App\Http\Resources\Blog;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $userId = $request->user()?->id;

        return [
            'current_page' => $this->resource->currentPage(),
            'data' => $this->collection->map(function ($blog) use ($userId) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'description' => $blog->description,
                    'cover_image' => FileUploadService::getFileUrl($blog->cover_image),
                    'user_id' => $blog->user_id,
                    'created_at' => $blog->created_at,
                    'updated_at' => $blog->updated_at,
                    'likes_count' => $blog->likes_count,
                    'is_liked_by_user' => $userId ? $blog->isLikedBy($userId) : false,
                    'user' => $blog->user ? [
                        'id' => $blog->user->id,
                        'name' => $blog->user->name,
                    ] : null,
                ];
            }),
            'per_page' => $this->resource->perPage(),
            'total' => $this->resource->total(),
        ];
    }
}
