<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->foreignId('parent_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('name', 255);
            $table->foreignId('folder_id')->default(0);
            $table->string('mime_type', 120);
            $table->integer('size');
            $table->string('url', 255);
            $table->text('options')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('media_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 120);
            $table->text('value')->nullable();
            $table->foreignId('media_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_folders');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('media_settings');
    }
};
