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
        Schema::create('link_stats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained();
            $table->foreignUuid('link_id')->constrained()->onDelete('cascade');

            $table->string('event')->nullable();

            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();

            $table->string('os')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('language')->nullable();

            $table->ipAddress('ip')->nullable();

            $table->string('utm_medium')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();

            $table->string('referer', 900)->nullable();

            $table->timestamps();

            // A link's own totals are read on every page of the links list, and
            // the dashboard slices the workspace by period. Without these two
            // both are a sequential scan of every event ever recorded.
            $table->index(['link_id', 'event']);
            $table->index(['workspace_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_stats');
    }
};
