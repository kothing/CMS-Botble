<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('audit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('module', 60)->index();
            $table->text('request')->nullable();
            $table->string('action', 120);
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 39)->nullable();
            $table->foreignId('reference_user');
            $table->foreignId('reference_id');
            $table->string('reference_name', 255);
            $table->string('type', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_histories');
    }
};
