<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('procurement_requests');

        Schema::create('procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('category')->default('Other');
            $table->decimal('estimated_cost', 14, 2);
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Purchased'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('procurement_requests');
    }
};
