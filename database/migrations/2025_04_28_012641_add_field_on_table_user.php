<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldOnTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('email');
            $table->string('division')->nullable()->after('username');
            $table->string('section')->nullable()->after('division');
            $table->string('mobile_no')->nullable()->after('section');
            $table->dropUnique(['email']);
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
     

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'division',
                'section',
                'mobile_no',
            ]);
            $table->unique('email');
        });
    }
}
