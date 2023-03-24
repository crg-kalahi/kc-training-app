<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingResourcePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_resource_people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->softDeletes();
            $table->string('training_id');
            $table->boolean('is_internal')->default(true);
            $table->boolean('is_female')->nullable();
            $table->string('position', 50)->nullable();
            $table->string('lname', 50);
            $table->string('fname', 50);
            $table->string('mname', 50)->nullable();
            $table->string('ext_name', 50)->nullable();
            $table->string('topic');
            $table->string('encoded_by');
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
        Schema::dropIfExists('training_resource_people');
    }
}
