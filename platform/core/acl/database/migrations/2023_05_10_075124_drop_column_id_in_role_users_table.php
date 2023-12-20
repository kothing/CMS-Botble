<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('role_users', 'id')) {
            Schema::table('role_users', function (Blueprint $table) {
                $table->dropColumn('id');
            });
        }

        Schema::dropIfExists('role_users_tmp');
        DB::statement('CREATE TABLE IF NOT EXISTS role_users_tmp LIKE role_users');
        DB::statement('TRUNCATE TABLE role_users_tmp');
        DB::statement('INSERT role_users_tmp SELECT * FROM role_users');
        DB::statement('TRUNCATE TABLE role_users');

        Schema::table('role_users', function (Blueprint $table) {
            $table->primary(['user_id', 'role_id']);
        });

        DB::table('role_users_tmp')->oldest()->chunk(1000, function ($chunked) {
            DB::table('role_users')->insertOrIgnore(array_map(fn($item) => (array)$item, $chunked->toArray()));
        });

        Schema::dropIfExists('role_users_tmp');
    }

    public function down(): void
    {
        Schema::table('role_users', function (Blueprint $table) {
            $table->dropPrimary(['user_id', 'role_id']);
        });

        Schema::table('role_users', function (Blueprint $table) {
            $table->id();
        });
    }
};
