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
        Schema::table('interns', function (Blueprint $table) {
            $table->string('year_level')
                ->after('student_number')
                ->nullable();
            $table->string('college')
                ->nullable();
            $table->string('program')
                ->nullable();
            $table->string('section')
                ->nullable();
            $table->unsignedBigInteger('coordinator_id');
            $table->foreign('coordinator_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
