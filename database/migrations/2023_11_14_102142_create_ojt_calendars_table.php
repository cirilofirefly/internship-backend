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
        Schema::create('ojt_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date')->format('Y-m-d');
            $table->string("note");
            $table->boolean("is_working_day");
            $table->unsignedBigInteger('supervisor_id');
            $table->foreign('supervisor_id')
                ->references('id')
                ->on('users');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ojt_calendars');
    }
};
