<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('locations', 'day_id')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropForeign(['day_id']);
            });
        }

        Schema::drop('days');
    }
};
