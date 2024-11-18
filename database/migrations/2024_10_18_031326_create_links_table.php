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
        Schema::create('links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained();

            $table->string('domain'); // domain of the link (e.g. lua.sh)
            $table->string('key'); // key of the link (e.g. /github)
            $table->string('url'); // target url (e.g. https://github.com/luadotsh)
            $table->string('link', 600)->unique(); // full link (e.g. https://lua.sh/github)

            // utm parameters
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_name')->nullable();

            $table->unsignedBigInteger('clicks')->default(0);
            $table->dateTime('last_click')->nullable();

            $table->string('external_id')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();

            $table->unique(['domain', 'key']);
            $table->unique(['workspace_id', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
