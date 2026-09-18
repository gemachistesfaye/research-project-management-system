<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('irerc_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->enum('risk_level', ['Low', 'Medium', 'High'])->default('Low');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Conditional'])->default('Pending');
            $table->string('clearance_code')->nullable()->unique();
            $table->text('committee_notes')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('irerc_clearances');
    }
};

