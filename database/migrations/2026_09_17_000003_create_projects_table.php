<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->string('title', 500);
            $table->text('abstract_text');
            $table->foreignId('thematic_id')->constrained('thematic_areas')->onDelete('cascade');
            $table->foreignId('pi_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dept_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->decimal('requested_budget', 14, 2)->default(0.00);
            $table->decimal('approved_budget', 14, 2)->nullable();
            $table->string('status', 50)->default('Draft');
            $table->tinyInteger('current_stage')->default(1);
            $table->boolean('ethical_cleared')->default(false);
            $table->string('proposal_document_url')->nullable();
            $table->timestamp('contract_signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};

