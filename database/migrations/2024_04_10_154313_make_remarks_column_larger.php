<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class {
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->longText('remarks')->change();
        });
    }

    public function down()
    {

    }
};
