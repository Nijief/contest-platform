<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function view(User $user, Submission $submission): bool
    {
        return $user->isJury() || $submission->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isParticipant();
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->isParticipant() && 
               $submission->user_id === $user->id && 
               $submission->canBeEdited();
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->isAdmin() || 
               ($user->isParticipant() && 
                $submission->user_id === $user->id && 
                $submission->status === Submission::STATUS_DRAFT);
    }
}