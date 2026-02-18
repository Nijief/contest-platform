<?php

namespace App\Events;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $submission;
    public $changedBy;

    public function __construct(Submission $submission, ?User $changedBy = null)
    {
        $this->submission = $submission;
        $this->changedBy = $changedBy;
    }
}