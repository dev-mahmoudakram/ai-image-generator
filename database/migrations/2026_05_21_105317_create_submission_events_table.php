<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('submission_id')
                ->constrained('submissions')
                ->cascadeOnDelete();

            $table->string('event_type', 64)->index();
            $table->json('payload')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['submission_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_events');
    }
};
