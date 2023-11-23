<?php

use App\Models\Address;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('day_cares', function (Blueprint $table) {
            $table->foreignIdFor(Address::class)->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('day_cares', function (Blueprint $table) {
            $table->dropForeignIdFor(Address::class);
        });
    }
};
