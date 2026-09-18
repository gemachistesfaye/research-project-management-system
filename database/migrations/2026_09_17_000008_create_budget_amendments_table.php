<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('budget_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->decimal('delta_amount', 14, 2);
            $table->text('justification');
            $table->string('proforma_document_url')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_amendments');
    }
};

