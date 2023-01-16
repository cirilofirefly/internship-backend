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
        Schema::table('users', function (Blueprint $table) {
            $table->string('middle_name')
                ->after('first_name')
                ->nullable();
            $table->string('suffix')
                ->after('last_name')
                ->nullable();
            $table->string('gender')
                ->after('suffix')
                ->nullable();
            $table->string('birthday')
                ->after('gender');
            $table->string('contact_number')
                ->after('birthday')
                ->nullable();
            $table->string('civil_status')
                ->after('contact_number');
            $table->string('profile_picture')
                ->after('password')
                ->nullable();
            $table->string('e_signature')
                ->after('profile_picture')
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('middle_name');
            $table->dropColumn('suffix');
            $table->dropColumn('gender');
            $table->dropColumn('birthday');
            $table->dropColumn('contact_number');
            $table->dropColumn('civil_status');
            $table->dropColumn('profile_picture');
            $table->dropColumn('e_signature');
        });
    }
};
