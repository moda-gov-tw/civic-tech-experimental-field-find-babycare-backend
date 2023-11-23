<?php

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrative_group_user', function (Blueprint $table) {
            $table->foreignIdFor(AdministrativeGroup::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->enum('role', AdministrativeGroupMemberRole::values());
            $table->timestamps();
            $table->unique(['administrative_group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_group_user');
    }
};
