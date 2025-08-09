<?php

namespace App\Http\Resources\Blog;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = $request->user() ? $request->user()->id : null;
        $blogs = $this->resource['blogs'];
        
        return [
            'status' => true,
            'message' => $this->resource['message'] ?? 'Blogs retrieved successfully',
            'data' => [
                'current_page' => $this->resource['blogs']->currentPage(),
                'data' => new BlogResource($blog),
                'per_page' => $this->resource['blogs']->perPage(),
                'total' => $this->resource['blogs']->total(),
            ]
        ];
    }
}
