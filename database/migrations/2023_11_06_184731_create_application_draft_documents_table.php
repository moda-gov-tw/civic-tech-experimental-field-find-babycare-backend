<?php

use App\Enums\ApplicationDocumentType;
use App\Models\ApplicationDraft;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_draft_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ApplicationDraft::class)->constrained()->cascadeOnDelete();
            $table->enum('type', ApplicationDocumentType::values());
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_draft_documents');
    }
};
