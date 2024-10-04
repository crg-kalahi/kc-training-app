<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV2EvaluationTrainingView extends Migration
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
            ALGORITHM = UNDEFINED VIEW `v2_evaluation_training_view` AS
            SELECT
                `evaluation_key_areas`.`id` AS `id`,
                `evaluation_trainings`.`training_id` AS `training_id`,
                SUM(IF(`evaluation_key_areas`.`stat` = 1, 1, 0)) AS `poor`,
                SUM(IF(`evaluation_key_areas`.`stat` = 2, 1, 0)) AS `fair`,
                SUM(IF(`evaluation_key_areas`.`stat` = 3, 1, 0)) AS `satisfactory`,
                SUM(IF(`evaluation_key_areas`.`stat` = 4, 1, 0)) AS `very_satisfactory`,
                SUM(IF(`evaluation_key_areas`.`stat` = 5, 1, 0)) AS `outstanding`,
                AVG(`evaluation_key_areas`.`stat`) AS `stat`,
                `evaluation_key_areas`.`area_training_id` AS `area_training_id`,
                `key_trainings`.`order` AS `order`,
                `key_trainings`.`title` AS `title`
            FROM
                `evaluation_key_areas`
            JOIN `evaluation_trainings` ON
                `evaluation_key_areas`.`evaluation_id` = `evaluation_trainings`.`id`
            JOIN `key_trainings` ON
                `evaluation_key_areas`.`area_training_id` = `key_trainings`.`id`
            WHERE
                `evaluation_key_areas`.`deleted_at` IS NULL
            GROUP BY
                `evaluation_trainings`.`training_id`,
                `key_trainings`.`id`;

        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS `v2_evaluation_training_view`');
    }
}
