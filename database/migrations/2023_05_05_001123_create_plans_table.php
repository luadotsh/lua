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
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('internal_id')->unique();
            $table->integer('price');
            $table->boolean('is_monthly');
            $table->string('stripe_id')->nullable();
            $table->integer('access_level');
            $table->boolean('is_private');
            $table->integer('max_users')->nullable();
            $table->integer('max_tags')->nullable();
            $table->integer('max_domains')->nullable();
            $table->integer('max_links');
            $table->integer('max_events');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
