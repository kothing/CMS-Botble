<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('gallery_meta_translations')) {
            Schema::create('gallery_meta_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('gallery_meta_id');
                $table->text('images')->nullable();

                $table->primary(['lang_code', 'gallery_meta_id'], 'gallery_meta_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_meta_translations');
    }
};
