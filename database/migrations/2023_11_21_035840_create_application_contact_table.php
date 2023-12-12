<?php

use App\Models\Application;
use App\Models\Contact;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_contact', function (Blueprint $table) {
            $table->foreignIdFor(Application::class)->constrained();
            $table->foreignIdFor(Contact::class)->constrained();
            $table->timestamps();
            $table->unique(['application_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_contact');
    }
};
