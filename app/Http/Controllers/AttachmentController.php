<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Attachment;
use App\Services\AttachmentService;
use App\Http\Requests\AttachmentUploadRequest;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    protected $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function upload(AttachmentUploadRequest $request, Submission $submission)
    {
        try {
            $attachment = $this->attachmentService->upload(
                $request->file('file'),
                $submission,
                $request->user()
            );

            return response()->json($attachment, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function download(Attachment $attachment, Request $request)
    {
        try {
            $url = $this->attachmentService->getSignedUrl(
                $attachment,
                $request->user()
            );

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function destroy(Attachment $attachment, Request $request)
    {
        try {
            $this->attachmentService->delete($attachment, $request->user());
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}