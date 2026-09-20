<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'feedback')) {
                    $table->text('feedback')->nullable();
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'feedback')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('feedback');
            });
        }
    }
};
