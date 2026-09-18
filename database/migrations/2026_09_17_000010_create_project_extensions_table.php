<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->tinyInteger('extension_number'); // 1, 2, or 3
            $table->integer('requested_months')->default(6);
            $table->text('reason');
            $table->enum('approver_role', ['Coordinator', 'RCSC'])->default('Coordinator'); // 1st & 2nd = Coordinator; 3rd = RCSC
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_extensions');
    }
};

