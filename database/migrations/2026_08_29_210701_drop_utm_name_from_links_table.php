<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `utm_name` was never written by anything — not the create action, not the
     * update action, not a tool. It only travelled out through the API resource
     * as a permanent null.
     */
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->dropColumn('utm_name');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->string('utm_name')->nullable();
        });
    }
};
