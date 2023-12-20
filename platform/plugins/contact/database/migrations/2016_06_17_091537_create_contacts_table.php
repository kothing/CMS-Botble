<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_replies');

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('email', 60);
            $table->string('phone', 60)->nullable();
            $table->string('address', 120)->nullable();
            $table->string('subject', 120)->nullable();
            $table->longText('content');
            $table->string('status', 60)->default('unread');
            $table->timestamps();
        });

        Schema::create('contact_replies', function (Blueprint $table) {
            $table->id();
            $table->longText('message');
            $table->foreignId('contact_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_replies');
    }
};
