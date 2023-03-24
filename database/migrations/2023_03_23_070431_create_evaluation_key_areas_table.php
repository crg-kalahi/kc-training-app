<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationKeyAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_key_areas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->softDeletes();
            $table->string('evaluation_id');
            $table->unsignedBigInteger('area_training_id');
            $table->integer('stat');
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
        Schema::dropIfExists('evaluation_key_areas');
    }
}
