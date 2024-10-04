<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationLearningView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE
            ALGORITHM = UNDEFINED VIEW `evaluation_learning_view` AS
            SELECT
                `evaluation_trainings`.`id` AS `id`,
                `evaluation_trainings`.`training_id` AS `training_id`,
                `evaluation_key_learnings`.`learning_id` AS `learning_id`,
                `evaluation_key_learnings`.`answer` AS `answer`
            FROM
                `evaluation_trainings`
            JOIN `evaluation_key_learnings` ON
                `evaluation_trainings`.`id` = `evaluation_key_learnings`.`evaluation_id`
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS `evaluation_learning_view`');
    }
}
