<?php

use App\Models\ApplicationDraft;
use App\Models\Infant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_draft_infant', function (Blueprint $table) {
            $table->foreignIdFor(ApplicationDraft::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Infant::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['application_draft_id', 'infant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_draft_infant');
    }
};
