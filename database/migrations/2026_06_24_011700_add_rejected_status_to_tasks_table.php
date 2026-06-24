<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE tasks DROP CONSTRAINT IF EXISTS tasks_status_check");
        DB::statement("ALTER TABLE tasks ADD CONSTRAINT tasks_status_check CHECK (status IN ('new', 'ongoing', 'completed', 'late', 'rejected'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tasks DROP CONSTRAINT IF EXISTS tasks_status_check");
        DB::statement("ALTER TABLE tasks ADD CONSTRAINT tasks_status_check CHECK (status IN ('new', 'ongoing', 'completed', 'late'))");
    }
};
