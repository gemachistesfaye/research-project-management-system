<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('dh_screened_at')->nullable()->after('current_stage');
            $table->timestamp('under_review_at')->nullable()->after('dh_screened_at');
            $table->timestamp('approved_at')->nullable()->after('under_review_at');
            $table->timestamp('activated_at')->nullable()->after('approved_at');
            $table->timestamp('completed_at')->nullable()->after('activated_at');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['dh_screened_at', 'under_review_at', 'approved_at', 'activated_at', 'completed_at']);
        });
    }
};
