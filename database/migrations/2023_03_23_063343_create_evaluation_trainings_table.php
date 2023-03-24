<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_trainings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->softDeletes();
            $table->string('training_id');
            $table->boolean('is_female');
            $table->unsignedBigInteger('office_rep_id');
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
        Schema::dropIfExists('evaluation_trainings');
    }
}
