<?php

namespace App\Http\Controllers;

use App\Http\Requests\Blog\CommentRequest;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;

class BlogInteractionController extends Controller
{
    /**
     * Like or unlike a blog
     */
    public function toggleLike(Request $request, $blogId)
    {
        $blog = Blog::find($blogId);

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $userId = $request->user()->id;
        $existingLike = Like::where('user_id', $userId)
            ->where('blog_id', $blogId)
            ->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $message = 'Blog unliked successfully';
            $liked = false;
        } else {
            // Like
            Like::create([
                'user_id' => $userId,
                'blog_id' => $blogId
            ]);
            $message = 'Blog liked successfully';
            $liked = true;
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [
                'liked' => $liked,
                'likes_count' => $blog->likes()->count()
            ]
        ]);
    }

    /**
     * Add a comment to a blog
     */
    public function comment(CommentRequest $request, $blogId)
    {
        $blog = Blog::find($blogId);

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'blog_id' => $blogId,
            'content' => $request->content
        ]);

        $comment->load('user:id,name');

        return response()->json([
            'status' => true,
            'message' => 'Comment added successfully',
            'data' => [
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name
                    ]
                ]
            ]
        ], 201);
    }

    /**
     * Get comments for a blog
     */
    public function getComments(Request $request, $blogId)
    {
        $blog = Blog::find($blogId);

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $comments = $blog->comments()
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Comments retrieved successfully',
            'data' => [
                'current_page' => $comments->currentPage(),
                'data' => $comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name
                        ]
                    ];
                }),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ]
        ]);
    }
}
