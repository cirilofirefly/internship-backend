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
        Schema::create('dtr_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_time_record_id');
            $table->foreign('daily_time_record_id')
                ->references('id')
                ->on('daily_time_records')
                ->onDelete('cascade');
            $table->string('key');
            $table->longText('image_proof');
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
        Schema::dropIfExists('dtr_proofs');
    }
};
