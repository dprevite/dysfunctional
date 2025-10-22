<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('runs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('function_path')->nullable()->index();
            $table->string('runtime_path')->nullable()->index();

            $table->unsignedBigInteger('requested_at')->nullable()->index(); // When the HTTP request was made
            $table->unsignedBigInteger('responded_at')->nullable();          // How long between the HTTP request and response

            $table->unsignedBigInteger('started_at')->nullable()->index(); // When the container started
            $table->unsignedBigInteger('stopped_at')->nullable()->index(); // When the container stopped

            $table->uuid('build_id')->nullable(); // If we had to build the runtime

            $table->text('uri')->nullable();

            $table->integer('response_code')->nullable()->index();

            $table->unsignedInteger('cost')->nullable();

            // Possible values: 'running', 'completed', 'starting'
            $table->string('status', 20)->default('starting')->index();

            $table->boolean('is_success')->nullable();

            $table->text('command')->nullable();

            $table->timestamps();

            // Composite indexes for common queries
            $table->index(['function_path', 'started_at']);
            $table->index(['runtime_path', 'started_at']);
            $table->index(['status', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runs');
    }
};
