<?php

use App\Enums\DayCareCategory;
use App\Enums\DayCareType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('day_cares', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', DayCareType::values());
            $table->boolean('is_in_construction');
            $table->boolean('is_in_raffle');
            $table->boolean('is_accepting_applications');
            $table->enum('category', DayCareCategory::values());
            $table->string('operating_hours');
            $table->integer('capacity');
            $table->string('monthly_fees');
            $table->string('establishment_id');
            $table->string('phone');
            $table->string('fax');
            $table->string('email');
            $table->double('lat');
            $table->double('lon');
            $table->string('facebook_page_url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('day_cares');
    }
};
