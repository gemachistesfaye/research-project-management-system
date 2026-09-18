<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('thematic_areas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('General Research'); // e.g. Agriculture, Public Health, Tech Transfer, Community Service
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('thematic_areas');
    }
};

