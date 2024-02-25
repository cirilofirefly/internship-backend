<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE requirements CHANGE COLUMN type type ENUM('application-letter', 'resume', 'company-profile', 'letter-of-endorsement', 'memorandum-of-agreement', 'others') NOT NULL DEFAULT 'others'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE requirements CHANGE COLUMN type type ENUM('application-letter', 'resume', 'company-profile', 'letter-of-endorsement', 'memorandum-of-agreement') NOT NULL DEFAULT 'resume'");
    }
};
