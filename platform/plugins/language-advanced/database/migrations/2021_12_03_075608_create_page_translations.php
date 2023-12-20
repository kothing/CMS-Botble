<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('pages_translations')) {
            return;
        }

        Schema::create('pages_translations', function (Blueprint $table) {
            $table->string('lang_code');
            $table->foreignId('pages_id');
            $table->string('name', 255)->nullable();
            $table->string('description', 400)->nullable();
            $table->longText('content')->nullable();

            $table->primary(['lang_code', 'pages_id'], 'pages_translations_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages_translations');
    }
};
