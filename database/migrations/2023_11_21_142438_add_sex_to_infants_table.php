<?php

use App\Enums\InfantSex;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infants', function (Blueprint $table) {
            $table->enum('sex', InfantSex::values());
        });
    }

    public function down(): void
    {
        Schema::table('infants', function (Blueprint $table) {
            $table->dropColumn('sex');
        });
    }
};
