<?php

declare(strict_types=1);

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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->foreignUuid('plan_id')->constrained();

            // Ownership is one person, so it is a column rather than a role in
            // the membership pivot: a workspace cannot end up with two owners,
            // or none, by a pivot row being written or removed.
            // Nullable so the workspace can be created before its owner row
            // exists, and so deleting a user does not delete their workspaces.
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->integer('billing_cycle_start')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
