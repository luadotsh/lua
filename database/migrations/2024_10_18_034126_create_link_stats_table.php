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
            $table->foreignUuid('link_id')->constrained();

            $table->string('event')->nullable();

            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();

            $table->string('os')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('language')->nullable();

            $table->ipAddress('ip')->nullable();

            $table->string('referrer', 900)->nullable();

            $table->timestamps();
            $table->softDeletes();
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
