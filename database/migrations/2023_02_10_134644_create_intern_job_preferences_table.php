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
        Schema::create('intern_job_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluator_user_id');
            $table->foreign('evaluator_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('intern_user_id');
            $table->foreign('intern_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('job_preference');
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
        Schema::dropIfExists('intern_job_preferences');
    }
};
