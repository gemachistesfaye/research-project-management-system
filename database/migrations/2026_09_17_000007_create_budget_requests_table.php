<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('budget_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->integer('milestone_phase')->default(1);
            $table->decimal('requested_amount', 14, 2);
            $table->decimal('approved_amount', 14, 2)->nullable();
            $table->enum('approval_tier', ['Dean', 'RCSC_VP'])->default('Dean'); // Dean (<500k), RCSC_VP (>=500k)
            $table->enum('status', ['Pending', 'Approved', 'Released', 'Rejected', 'Refunded'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_requests');
    }
};

