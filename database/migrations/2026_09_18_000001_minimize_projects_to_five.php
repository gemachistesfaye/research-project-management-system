<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $keepIds = [1, 2, 12, 13, 14];

        // Disable foreign key checks for SQLite
        DB::statement('PRAGMA foreign_keys = OFF');

        // Get all tables with project_id column
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
        foreach ($tables as $table) {
            $name = $table->name;
            $columns = DB::select("PRAGMA table_info($name)");
            $hasProjectId = false;
            foreach ($columns as $col) {
                if ($col->name === 'project_id') {
                    $hasProjectId = true;
                    break;
                }
            }
            if ($hasProjectId && $name !== 'projects') {
                DB::statement("DELETE FROM \"$name\" WHERE project_id NOT IN (" . implode(',', $keepIds) . ")");
            }
        }

        // Delete the projects themselves
        DB::statement("DELETE FROM projects WHERE project_id NOT IN (" . implode(',', $keepIds) . ")");

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // This migration is destructive and cannot be reversed
    }
};
