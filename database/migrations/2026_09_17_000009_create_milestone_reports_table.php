<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('milestone_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->string('milestone_name');
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->text('summary_text');
            $table->string('deliverable_document_url')->nullable();
            $table->enum('status', ['Submitted', 'Coordinator_Audited', 'Approved', 'Needs_Revision'])->default('Submitted');
            $table->text('coordinator_feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('milestone_reports');
    }
};

