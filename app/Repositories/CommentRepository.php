<?php

namespace App\Repositories;

use App\Http\Resources\Comment\CommentListResource;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentRepository
{
    /**
     * Stores a new comment for the specified commentable entity.
     *
     * @param Request $request The HTTP request containing comment data.
     * @param int $id The ID of the commentable entity.
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If the commentable entity is not found.
     *
     * request က ပါလာတဲ့ type က project or task ဖြစ်ရမယ်
     * type က project ဖြစ်ရင် project id ကို ရှာပြီး commentable အဖြစ် သတ်မှတ်ထားတဲ့ project မှာ comment တစ်ခု ထည့်ပေးမယ်
     * type က task ဖြစ်ရင် task id ကို ရှာပြီး commentable အဖြစ် သတ်မှတ်ထားတဲ့ task မှာ comment တစ်ခု ထည့်ပေးမယ်
     */
    public function store(Request $request, $id)
    {
        $commentable = $this->getCommentable($request->input('type'), $id);
        abort_if(!$commentable, 404, $request->input('type') . ' not found.');
        $authUser = auth_user();

        $commentable->comments()->create([
            'content' => $request->input('content'),
            'parent_id' => $request->input('parent_id'),
            'comment_by' => $authUser->id,
        ]);

        return response()->json(['message' => 'Comment added successfully.'], 201);
    }

    /**
     * get comments for the specified commentable entity.
     * @param Request $request The HTTP request containing pagination and filter data.
     * @param int $id The ID of the commentable entity.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection Collection of comments.
     */
    public function getComments($request, $id)
    {
        $perPage = $request->input('per_page', 10);
        $commentable = $this->getCommentable($request->input('type'), $id);

        $comments = $commentable->comments()
            ->with(['replies'])
            ->onlyParent()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return CommentListResource::collection($comments);
    }


    /**
     * ထည့်ပေးတဲ့ type နဲ့ id ပေါ် မူတည်ပြီး project or task ကို ရှာဖွေတဲ့ function
     * နောက်ပိုင်း comment နဲ့ တွဲသုံးမယ့်ကောင်များလာရင် trait ခွဲ‌ရေးရမယ်
     */
    public function getCommentable($type, $id)
    {
        if ($type == Comment::PROJECT) {
            return Project::find($id);
        } else {
            return Task::find($id);
        }
    }
}
