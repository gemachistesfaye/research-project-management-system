<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('status');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->text('admin_cancel_notes')->nullable()->after('cancelled_at');
            $table->boolean('cancelled_by_pi')->default(false)->after('admin_cancel_notes');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'cancelled_at', 'admin_cancel_notes', 'cancelled_by_pi']);
        });
    }
};
