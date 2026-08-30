<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Who created a link was never recorded, so a shared workspace had no way
     * to answer "which of these are mine?". Nullable, because every link that
     * already exists predates the column and because a link can outlive the
     * account that made it.
     */
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->foreignUuid('user_id')->nullable()->after('workspace_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
