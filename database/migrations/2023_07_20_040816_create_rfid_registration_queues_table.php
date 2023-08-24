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
        Schema::create('rfid_registration_queues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('intern_user_id');
            $table->foreign('intern_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('coordinator_user_id');
            $table->foreign('coordinator_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('device_token');
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
        Schema::dropIfExists('rfid_registration_queues');
    }
};
