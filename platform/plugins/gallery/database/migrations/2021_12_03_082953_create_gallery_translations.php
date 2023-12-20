<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('galleries_translations')) {
            Schema::create('galleries_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('galleries_id');
                $table->string('name', 255)->nullable();
                $table->longText('description')->nullable();

                $table->primary(['lang_code', 'galleries_id'], 'galleries_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries_translations');
    }
};
