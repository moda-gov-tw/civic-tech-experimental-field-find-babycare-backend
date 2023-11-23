<?php

use App\Enums\DayCareMemberRole;
use App\Models\DayCare;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('day_care_user', function (Blueprint $table) {
            $table->foreignIdFor(DayCare::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->enum('role', DayCareMemberRole::values());
            $table->timestamps();
            $table->unique(['day_care_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('day_care_user');
    }
};
