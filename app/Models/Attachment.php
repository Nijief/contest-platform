<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_SCANNED = 'scanned';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'submission_id',
        'user_id',
        'original_name',
        'mime',
        'size',
        'storage_key',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isScanned(): bool
    {
        return $this->status === self::STATUS_SCANNED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}