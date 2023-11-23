<?php

use App\Enums\InfantStatusType;
use App\Models\Infant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infant_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Infant::class)->constrained()->cascadeOnDelete();
            $table->enum('type', InfantStatusType::values());
            $table->timestamps();
            $table->unique(['infant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infant_statuses');
    }
};
