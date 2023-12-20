<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('meta_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('meta_key', 255);
            $table->text('meta_value')->nullable();
            $table->foreignId('reference_id')->index();
            $table->string('reference_type', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_boxes');
    }
};
