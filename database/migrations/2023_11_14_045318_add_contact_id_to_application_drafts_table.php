<?php

use App\Models\Contact;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_drafts', function (Blueprint $table) {
            $table->foreignIdFor(Contact::class)->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('application_drafts', function (Blueprint $table) {
            $table->dropForeignIdFor(Contact::class);
        });
    }
};
