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
        
        $userId = $request->user() ? $request->user()->id : null;

        return [
            'status' => true,
            'message' => $this->resource['message'] ?? 'Blog retrieved successfully',
            'data' => [
                'id' => $this->id,
                'title' => $this->title,
                'slug' => $this->slug,
                'description' => $this->description,
                'cover_image' => FileUploadService::getFileUrl($blog->cover_image),
                'user_id' => $this->user_id,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'likes_count' => $this->likes_count,
                'is_liked_by_user' => $userId ? $this->isLikedBy($userId) : false,
                'user' => $this->user ? [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ] : null,
                'comments' => $this->comments()->with('user:id,name')->latest()->get()->map(function ($comment) {
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
