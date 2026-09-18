<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id('eval_id');
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->foreignId('examiner_id')->constrained('users')->onDelete('cascade');
            $table->decimal('score', 5, 2)->default(0.00);
            $table->enum('decision', [
                'Pending',
                'Accepted',
                'AcceptedWithMinorMods',
                'AcceptedWithMajorMods',
                'Rejected'
            ])->default('Pending');
            $table->text('comments')->nullable();
            $table->boolean('is_blind_masked')->default(true);
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};

