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
        Schema::table('intern_job_preferences',function (Blueprint $table){
            $table->dropColumn("job_preference");
            // $table->longText('evaluation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('intern_job_preferences',function (Blueprint $table){
            // $table->dropColumn("evaluation");
            $table->string('job_preference');
        });
    }
};
