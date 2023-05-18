<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Make0To1AsDefaultValueToOfficeRepEvalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('evaluation_trainings')->where('office_rep_id', 0)
            ->update(['office_rep_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('evaluation_trainings')->where('office_rep_id', 1)
            ->update(['office_rep_id' => 0]);
    }
}
