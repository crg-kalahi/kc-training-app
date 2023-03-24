<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->softDeletes();
            $table->string('training_id');
            $table->string('email')->nullable();
            $table->boolean('is_internal')->default(true);
            $table->boolean('is_female')->nullable();
            $table->string('position', 50)->nullable();
            $table->string('lname', 50);
            $table->string('fname', 50);
            $table->string('mname', 50)->nullable();
            $table->string('ext_name', 50)->nullable();
            $table->integer('pre_test')->nullable();
            $table->integer('post_test')->nullable();
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
        Schema::dropIfExists('training_participants');
    }
}
