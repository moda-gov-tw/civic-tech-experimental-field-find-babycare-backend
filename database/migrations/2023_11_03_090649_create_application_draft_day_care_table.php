<?php

use App\Models\ApplicationDraft;
use App\Models\DayCare;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_draft_day_care', function (Blueprint $table) {
            $table->foreignIdFor(ApplicationDraft::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(DayCare::class)->constrained()->cascadeOnDelete();
            $table->boolean('is_public_housing_resident')->default(false);
            $table->boolean('is_school_employee_child')->default(false);
            $table->timestamps();
            $table->unique(['application_draft_id', 'day_care_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_draft_day_care');
    }
};
