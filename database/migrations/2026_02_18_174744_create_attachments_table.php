<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('mime');
            $table->unsignedInteger('size');
            $table->string('storage_key')->unique();
            $table->enum('status', ['pending', 'scanned', 'rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->index(['submission_id', 'status']);
            $table->index('storage_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};