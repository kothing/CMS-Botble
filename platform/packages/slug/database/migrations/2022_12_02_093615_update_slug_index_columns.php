<?php

use Botble\Slug\Models\Slug;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        Schema::table('slugs', function (Blueprint $table) use ($sm) {
            if (! $sm->introspectTable($table->getTable())->hasIndex('slugs_reference_id_index')) {
                $table->index('reference_id');
            }
        });

        try {
            foreach (Slug::get() as $slug) {
                if ($slug->reference_type && class_exists(
                    $slug->reference_type
                ) && (! $slug->reference || ! $slug->reference->id)) {
                    $slug->delete();
                }
            }
        } catch (Throwable) {
        }
    }

    public function down(): void
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        Schema::table('slugs', function (Blueprint $table) use ($sm) {
            if ($sm->introspectTable($table->getTable())->hasIndex('reference_id')) {
                $table->dropIndex('reference_id');
            }
        });
    }
};
