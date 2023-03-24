<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationKeyResourcePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_key_resource_people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->softDeletes();
            $table->string('evaluation_id');
            $table->string('rp_id')->comment('from training_resource_people');
            $table->unsignedBigInteger('area_rp_id');
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
        Schema::dropIfExists('evaluation_key_resource_people');
    }
}
