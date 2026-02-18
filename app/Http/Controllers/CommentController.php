<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\SubmissionService;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Добавить этот импорт

class CommentController extends Controller
{
    use AuthorizesRequests; // Добавить эту строку
    
    protected $submissionService;

    public function __construct(SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    public function index(Submission $submission)
    {
        $this->authorize('view', $submission); // Теперь работает

        return response()->json(
            $submission->comments()->with('user')->latest()->get()
        );
    }

    public function store(CommentRequest $request, Submission $submission)
    {
        $comment = $this->submissionService->addComment(
            $submission,
            $request->body,
            $request->user()
        );

        return response()->json($comment->load('user'), 201);
    }
}