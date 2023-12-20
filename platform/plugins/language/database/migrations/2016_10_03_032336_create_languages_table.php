<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id('lang_id');
            $table->string('lang_name', 120);
            $table->string('lang_locale', 20);
            $table->string('lang_code', 20);
            $table->string('lang_flag', 20)->nullable();
            $table->tinyInteger('lang_is_default')->unsigned()->default(0);
            $table->integer('lang_order')->default(0);
            $table->tinyInteger('lang_is_rtl')->unsigned()->default(0);
        });

        Schema::create('language_meta', function (Blueprint $table) {
            $table->id('lang_meta_id');
            $table->text('lang_meta_code')->nullable();
            $table->string('lang_meta_origin', 255);
            $table->foreignId('reference_id')->index();
            $table->string('reference_type', 120);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
        Schema::dropIfExists('language_meta');
    }
};
