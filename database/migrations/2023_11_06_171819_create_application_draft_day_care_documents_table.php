<?php

use App\Enums\ApplicationDayCareDocumentType;
use App\Models\ApplicationDraft;
use App\Models\DayCare;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_draft_day_care_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ApplicationDraft::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(DayCare::class)->constrained()->cascadeOnDelete();
            $table->enum('type', ApplicationDayCareDocumentType::values());
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_draft_day_care_documents');
    }
};
