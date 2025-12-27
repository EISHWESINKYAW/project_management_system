<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Repositories\CommentRepository;

class CommentController extends Controller
{

    protected $repo;

    public function __construct(CommentRepository $repo)
    {
        $this->repo = $repo;
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'type' => 'required|in:' . implode(',', Comment::COMMENTABLE_TYPES),
        ]);

        return $this->repo->store($request, $id);
    }

    public function getComments(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:'. implode(',', Comment::COMMENTABLE_TYPES)
        ]);

        return $this->repo->getComments($request,$id);
    }
}
