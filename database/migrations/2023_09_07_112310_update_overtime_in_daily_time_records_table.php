<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->dropColumn('overtime');
            $table->string("overtime_start_time")
                ->after('pm_start_time')
                ->nullable();
            $table->string("overtime_end_time")
                ->after('overtime_start_time')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->string('overtime');
            $table->dropColumn('overtime_start_time');
            $table->dropColumn('overtime_end_time');
        });
    }
};
