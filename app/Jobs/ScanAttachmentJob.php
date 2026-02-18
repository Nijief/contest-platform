<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScanAttachmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attachment;

    public function __construct(Attachment $attachment)
    {
        $this->attachment = $attachment;
    }

    public function handle(AttachmentService $attachmentService): void
    {
        try {
            // Имитация проверки файла
            $this->validateFile($this->attachment);

            $attachmentService->markScanned($this->attachment);
            
            Log::info('Attachment scanned successfully', [
                'attachment_id' => $this->attachment->id,
                'submission_id' => $this->attachment->submission_id,
            ]);

        } catch (\Exception $e) {
            $attachmentService->reject($this->attachment, $e->getMessage());
            
            Log::error('Attachment scan failed', [
                'attachment_id' => $this->attachment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function validateFile(Attachment $attachment): void
    {
        $allowedMimes = ['application/pdf', 'application/zip', 'image/png', 'image/jpeg'];
        
        if (!in_array($attachment->mime, $allowedMimes)) {
            throw new \Exception('Invalid file type');
        }

        if ($attachment->size > 10 * 1024 * 1024) {
            throw new \Exception('File size exceeds 10MB');
        }

        $forbiddenNames = ['malware', 'virus', 'hack'];
        foreach ($forbiddenNames as $name) {
            if (stripos($attachment->original_name, $name) !== false) {
                throw new \Exception('Filename contains forbidden word');
            }
        }
    }
}