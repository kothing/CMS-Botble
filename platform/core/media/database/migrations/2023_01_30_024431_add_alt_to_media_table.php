<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('media_files', 'alt')) {
            Schema::table('media_files', function (Blueprint $table) {
                $table->string('alt')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('media_files', 'alt')) {
            Schema::table('media_files', function (Blueprint $table) {
                $table->dropColumn('alt');
            });
        }
    }
};
