<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('budget_requests', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('disbursed_at');
            $table->text('notes')->nullable()->after('payment_method');
        });
    }

    public function down()
    {
        Schema::table('budget_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'notes']);
        });
    }
};
