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
            $table->dropColumn('is_submitted');
            $table->dropColumn('is_approved');
            $table->enum("status", ['default', 'submitted', 'validated'])
                ->default('default');
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
            $table->boolean("is_submitted")
                ->default(false);
            $table->boolean("is_approved")
                ->default(false);
            $table->dropColumn('status');

        });
    }
};
