<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationTrainingView extends Migration
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
            ALGORITHM = UNDEFINED VIEW `evaluation_training_view` AS
            SELECT
                `evaluation_key_areas`.`id` AS `id`,
                `evaluation_trainings`.`training_id` AS `training_id`,
                `evaluation_key_areas`.`stat` AS `stat`,
                COUNT(`evaluation_key_areas`.`stat`) AS `total_count`,
                `evaluation_key_areas`.`area_training_id` AS `area_training_id`,
                `key_trainings`.`order` AS `order`,
                `key_trainings`.`title` AS `title`
            FROM
                `evaluation_key_areas`
            JOIN `evaluation_trainings` ON
                `evaluation_key_areas`.`evaluation_id` = `evaluation_trainings`.`id`
            JOIN `key_trainings` ON
                `evaluation_key_areas`.`area_training_id` = `key_trainings`.`id`
            GROUP BY
                `evaluation_trainings`.`training_id`,
                `key_trainings`.`id`,
                `evaluation_key_areas`.`stat`;

        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS `evaluation_training_view`');
    }
}
