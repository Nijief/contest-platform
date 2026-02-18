<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\User;
use App\Models\Notification; // Добавить этот импорт
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyStatusChangedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $submission;
    protected $changedBy;

    public function __construct(Submission $submission, ?User $changedBy = null)
    {
        $this->submission = $submission;
        $this->changedBy = $changedBy;
    }

    public function handle(): void
    {
        $this->sendEmailNotification();
        $this->createDatabaseNotification();
        $this->logNotification();
    }

    protected function sendEmailNotification(): void
    {
        $user = $this->submission->user;
        $status = $this->submission->status;
        $contest = $this->submission->contest;

        $data = [
            'user_name' => $user->name,
            'submission_title' => $this->submission->title,
            'contest_title' => $contest->title,
            'status' => $status,
            'changed_by' => $this->changedBy?->name ?? 'System',
        ];

        Log::info('Email notification would be sent', $data);
    }

    protected function createDatabaseNotification(): void
    {
        Notification::create([ // Теперь работает
            'user_id' => $this->submission->user_id,
            'type' => 'submission_status_changed',
            'data' => json_encode([
                'submission_id' => $this->submission->id,
                'submission_title' => $this->submission->title,
                'contest_id' => $this->submission->contest_id,
                'contest_title' => $this->submission->contest->title,
                'old_status' => $this->submission->getOriginal('status'),
                'new_status' => $this->submission->status,
                'changed_by' => $this->changedBy?->name ?? 'System',
                'changed_by_id' => $this->changedBy?->id,
            ]),
        ]);
    }

    protected function logNotification(): void
    {
        Log::info('Submission status changed notification', [
            'submission_id' => $this->submission->id,
            'user_id' => $this->submission->user_id,
            'new_status' => $this->submission->status,
            'changed_by' => $this->changedBy?->id,
        ]);
    }
}