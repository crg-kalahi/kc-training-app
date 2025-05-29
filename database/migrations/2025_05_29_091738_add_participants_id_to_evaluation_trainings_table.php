<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParticipantsIdToEvaluationTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluation_trainings', function (Blueprint $table) {
            $table->uuid('participants_id')->after('id');
        });
    }

    public function down()
    {
        Schema::table('evaluation_trainings', function (Blueprint $table) {
            $table->dropColumn('participants_id');
        });
    }
}
