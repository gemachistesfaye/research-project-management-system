<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('irerc_clearances')) {
            $records = DB::table('irerc_clearances')->get()->toArray();
            Schema::dropIfExists('irerc_clearances');

            Schema::create('irerc_clearances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
                $table->string('risk_level', 50)->default('Low');
                $table->string('status', 50)->default('Pending');
                $table->string('clearance_code')->nullable()->unique();
                $table->text('committee_notes')->nullable();
                $table->timestamp('issued_at')->nullable();
                $table->timestamps();
            });

            foreach ($records as $record) {
                DB::table('irerc_clearances')->insert((array) $record);
            }
        }
    }

    public function down()
    {
    }
};

