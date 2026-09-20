<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('certificates')) {
            $records = DB::table('certificates')->get()->toArray();
            Schema::dropIfExists('certificates');

            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
                $table->string('certificate_code')->unique();
                $table->string('issued_to_name');
                $table->string('type', 50)->default('Completion');
                $table->string('pdf_url')->nullable();
                $table->timestamp('issued_at')->nullable();
                $table->timestamps();
            });

            foreach ($records as $record) {
                DB::table('certificates')->insert((array) $record);
            }
        }
    }

    public function down()
    {
    }
};

