<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('milestone_reports', function (Blueprint $table) {
            $table->string('deliverable_file_path', 500)->nullable()->after('summary_text');
            $table->string('deliverable_link_url', 500)->nullable()->after('deliverable_file_path');
        });
    }

    public function down()
    {
        Schema::table('milestone_reports', function (Blueprint $table) {
            $table->dropColumn(['deliverable_file_path', 'deliverable_link_url']);
        });
    }
};
