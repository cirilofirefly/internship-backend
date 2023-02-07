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
        Schema::create('daily_time_records', function (Blueprint $table) {
            $table->id();
            $table->string("date");
            $table->string("am_start_time");
            $table->string("am_end_time");
            $table->string("pm_start_time");
            $table->string("pm_end_time");
            $table->string("description")
                ->nullable();
            $table->string("overtime")
                ->nullable();
            $table->boolean("is_submitted")
                ->default(false);
            $table->boolean("is_approved")
                ->nullable()
                ->default(null);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
        Schema::dropIfExists('daily_time_records');
    }
};
