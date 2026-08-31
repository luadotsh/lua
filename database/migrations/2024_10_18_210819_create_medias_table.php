<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Polymorphic: a media belongs to whatever model owns it.
            $table->uuidMorphs('mediable');

            // One named bucket per model, e.g. avatar or logo.
            $table->string('collection');

            $table->string('path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');

            // Width and height for images.
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id', 'collection']);
        });
    }
};
