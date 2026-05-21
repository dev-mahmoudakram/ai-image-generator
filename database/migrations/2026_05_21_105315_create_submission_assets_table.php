<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_assets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('submission_id')
                ->constrained('submissions')
                ->cascadeOnDelete();

            $table->string('kind', 16)->index();        // selfie | generated
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('mime_type', 64)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['submission_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_assets');
    }
};
