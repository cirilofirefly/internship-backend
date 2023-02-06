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
        Schema::create('detailed_reports', function (Blueprint $table) {
            $table->id();
            $table->longText('activities');
            $table->longText('learning');
            $table->unsignedBigInteger('date_time_record_id');
            $table->foreign('date_time_record_id')
                ->references('id')
                ->on('date_time_records')
                ->onDelete('cascade');
            $table->boolean("is_approved")
                ->nullable()
                ->default(null);
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
        Schema::dropIfExists('detailed_reports');
    }
};
