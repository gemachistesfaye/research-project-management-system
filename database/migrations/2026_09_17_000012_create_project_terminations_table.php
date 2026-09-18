<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_terminations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->text('reason');
            $table->decimal('total_disbursed', 14, 2)->default(0.00);
            $table->decimal('verified_deliverables_value', 14, 2)->default(0.00);
            $table->decimal('refund_due', 14, 2)->default(0.00);
            $table->enum('status', ['Pending', 'Terminated_Refounded', 'Terminated_WrittenOff'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_terminations');
    }
};

