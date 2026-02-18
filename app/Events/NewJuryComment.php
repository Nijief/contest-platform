<?php

namespace App\Events;

use App\Models\Submission;
use App\Models\SubmissionComment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewJuryComment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $submission;
    public $comment;

    public function __construct(Submission $submission, SubmissionComment $comment)
    {
        $this->submission = $submission;
        $this->comment = $comment;
    }
}