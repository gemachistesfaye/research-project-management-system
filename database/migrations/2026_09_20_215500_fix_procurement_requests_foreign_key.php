<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // Safe no-op: original migration used DROP/RECREATE which destroys all data.
        // The FK fix was for a bug in the original migration, but SQLite silently ignores
        // invalid FK references anyway. The table was already created by earlier migrations.
    }

    public function down()
    {
    }
};
