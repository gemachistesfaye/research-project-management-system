<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('procurement_requests', function (Blueprint $table) {
            $table->string('category')->default('Other')->after('description');
        });
    }

    public function down()
    {
        Schema::table('procurement_requests', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
