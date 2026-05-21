<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generation_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('submission_id')
                ->constrained('submissions')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('attempt_no')->default(1);

            $table->string('status', 16)->index();   // queued|processing|completed|failed

            $table->string('provider', 64)->nullable();
            $table->string('model', 128)->nullable();
            $table->text('prompt')->nullable();

            $table->foreignId('selfie_asset_id')
                ->nullable()
                ->constrained('submission_assets')
                ->restrictOnDelete();

            $table->foreignId('generated_asset_id')
                ->nullable()
                ->constrained('submission_assets')
                ->nullOnDelete();

            $table->text('error_message')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['submission_id', 'attempt_no']);
            $table->index(['submission_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generation_attempts');
    }
};
