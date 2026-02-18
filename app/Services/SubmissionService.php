<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use App\Models\SubmissionComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class SubmissionService
{
    public function create(array $data, User $user): Submission
    {
        return DB::transaction(function () use ($data, $user) {
            $submission = Submission::create([
                'contest_id' => $data['contest_id'],
                'user_id' => $user->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => Submission::STATUS_DRAFT,
            ]);

            return $submission;
        });
    }

    public function update(Submission $submission, array $data): Submission
    {
        if (!$submission->canBeEdited()) {
            throw new \Exception('Submission cannot be edited in its current status');
        }

        return DB::transaction(function () use ($submission, $data) {
            $submission->update([
                'title' => $data['title'] ?? $submission->title,
                'description' => $data['description'] ?? $submission->description,
            ]);

            return $submission->fresh();
        });
    }

    public function submit(Submission $submission): Submission
    {
        if (!$submission->canBeEdited()) {
            throw new \Exception('Submission cannot be submitted from its current status');
        }

        if (!$submission->hasMinimumAttachments()) {
            throw new \Exception('Submission must have at least one scanned attachment');
        }

        return DB::transaction(function () use ($submission) {
            $submission->update([
                'status' => Submission::STATUS_SUBMITTED,
            ]);

            event(new \App\Events\SubmissionStatusChanged($submission));

            return $submission->fresh();
        });
    }

    public function changeStatus(Submission $submission, string $newStatus, User $user): Submission
    {
        if (!$submission->canTransitionTo($newStatus)) {
            throw new \Exception('Invalid status transition');
        }

        if ($newStatus === Submission::STATUS_ACCEPTED && !$submission->hasMinimumAttachments()) {
            throw new \Exception('Submission must have at least one scanned attachment to be accepted');
        }

        return DB::transaction(function () use ($submission, $newStatus, $user) {
            $submission->update([
                'status' => $newStatus,
            ]);

            event(new \App\Events\SubmissionStatusChanged($submission, $user));

            return $submission->fresh();
        });
    }

    public function addComment(Submission $submission, string $body, User $user): SubmissionComment
    {
        return DB::transaction(function () use ($submission, $body, $user) {
            $comment = $submission->comments()->create([
                'user_id' => $user->id,
                'body' => $body,
            ]);

            if ($user->isJury()) {
                event(new \App\Events\NewJuryComment($submission, $comment));
            }

            return $comment;
        });
    }
}