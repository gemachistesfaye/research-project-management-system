<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('pi_signature_date')->nullable()->after('contract_signed_at');
            $table->timestamp('vp_signature_date')->nullable()->after('pi_signature_date');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['pi_signature_date', 'vp_signature_date']);
        });
    }
};
