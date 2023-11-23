<?php

use App\Enums\ApplicationStatus;
use App\Models\DayCare;
use App\Models\Infant;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(DayCare::class)->constrained();
            $table->foreignIdFor(Infant::class)->constrained();
            $table->enum('status', ApplicationStatus::values());
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
