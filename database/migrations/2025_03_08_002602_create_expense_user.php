<?php

use App\Models\Expenses;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expense_user', function (Blueprint $table) {
            $table->foreignIdFor(Expenses::class)->constrained('expenses');
            $table->foreignIdFor(User::class)->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_user');
    }
};
