<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\SubmissionService;
use App\Http\Requests\SubmissionRequest;
use App\Http\Requests\StatusChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubmissionController extends Controller
{
    use AuthorizesRequests;
    
    protected $submissionService;

    public function __construct(SubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isJury()) {
            $submissions = Submission::with(['user', 'contest', 'attachments'])
                ->latest()
                ->paginate(15);
        } else {
            $submissions = $user->submissions()
                ->with(['contest', 'attachments'])
                ->latest()
                ->paginate(15);
        }

        return response()->json($submissions);
    }

    public function store(SubmissionRequest $request)
    {
        try {
            $submission = $this->submissionService->create(
                $request->validated(),
                $request->user()
            );

            return response()->json($submission->load('attachments'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);
        
        return response()->json(
            $submission->load(['user', 'contest', 'attachments', 'comments.user'])
        );
    }

    public function update(SubmissionRequest $request, Submission $submission)
    {
        try {
            $submission = $this->submissionService->update(
                $submission,
                $request->validated()
            );

            return response()->json($submission);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function submit(Submission $submission)
    {
        $this->authorize('update', $submission);

        try {
            $submission = $this->submissionService->submit($submission);
            return response()->json($submission);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function changeStatus(StatusChangeRequest $request, Submission $submission)
    {
        try {
            $submission = $this->submissionService->changeStatus(
                $submission,
                $request->status,
                $request->user()
            );

            return response()->json($submission);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}