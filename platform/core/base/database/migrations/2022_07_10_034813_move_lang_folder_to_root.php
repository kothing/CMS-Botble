<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

return new class () extends Migration {
    public function up(): void
    {
        if (File::isDirectory(resource_path('lang'))) {
            File::moveDirectory(resource_path('lang'), base_path('lang'));
        }
    }
};
