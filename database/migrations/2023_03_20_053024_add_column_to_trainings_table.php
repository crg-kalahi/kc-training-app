<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->string('key_trainings')->nullable()->after('venue');
            $table->string('key_learnings')->nullable()->after('key_trainings');
            $table->string('key_rp')->nullable()->after('key_learnings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['key_trainings', 'key_learnings', 'key_rp']);
        });
    }
}
