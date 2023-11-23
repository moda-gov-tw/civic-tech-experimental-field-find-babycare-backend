<?php

use App\Models\AdministrativeGroup;
use App\Models\DayCare;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrative_group_day_care', function (Blueprint $table) {
            $table->foreignIdFor(AdministrativeGroup::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(DayCare::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['administrative_group_id', 'day_care_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_group_day_care');
    }
};
