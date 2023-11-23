<?php

use App\Enums\ApplicationInfantDocumentType;
use App\Models\Application;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_infant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Application::class)->constrained()->cascadeOnDelete();
            $table->enum('type', ApplicationInfantDocumentType::values());
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_infant_documents');
    }
};
