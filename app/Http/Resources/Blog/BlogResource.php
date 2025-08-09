<?php

namespace App\Http\Resources\Blog;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $blog = $this->resource;
        $userId = $request->user() ? $request->user()->id : null;

        return [
            'status' => true,
            'message' => $this->resource['message'] ?? 'Blog retrieved successfully',
            'data' => [
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
                'comments' => $blog->comments()->with('user:id,name')->latest()->get()->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name
                        ]
                    ];
                })
            ]
        ];
    }
}
