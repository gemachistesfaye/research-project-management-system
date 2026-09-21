<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // Safe no-op: original migration used DROP/RECREATE which risks data loss.
        // The enum->string change is cosmetic in SQLite (types are not enforced).
    }

    public function down()
    {
    }
};
