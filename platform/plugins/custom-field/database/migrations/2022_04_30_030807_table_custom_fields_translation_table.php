<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('custom_fields_translations')) {
            Schema::create('custom_fields_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('custom_fields_id');
                $table->text('value')->nullable();

                $table->primary(['lang_code', 'custom_fields_id'], 'custom_fields_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_fields_translations');
    }
};
