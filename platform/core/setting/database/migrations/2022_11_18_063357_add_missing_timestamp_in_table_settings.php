<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'created_at')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->timestamps();
            });
        }
    }
};
