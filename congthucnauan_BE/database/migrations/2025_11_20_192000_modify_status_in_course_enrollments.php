<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('course_enrollments')) {
            return;
        }

        // Modify status column to be VARCHAR(50) to accept 'pending', 'active', 'completed', 'cancelled'
        DB::statement("ALTER TABLE course_enrollments MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'active'");
    }

    public function down()
    {
        // No operation
    }
};
