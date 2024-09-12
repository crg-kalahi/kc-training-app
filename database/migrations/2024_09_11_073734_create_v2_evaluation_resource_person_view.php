<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV2EvaluationResourcePersonView extends Migration
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
            ALGORITHM = UNDEFINED VIEW `v2_evaluation_resource_person_view` AS
            SELECT
                `evaluation_trainings`.`id` AS `id`,
                `evaluation_trainings`.`deleted_at` AS `deleted_at`,
                `evaluation_trainings`.`training_id` AS `training_id`,
                `evaluation_key_resource_people`.`rp_id` AS `rp_id`,
                `evaluation_key_resource_people`.`area_rp_id` AS `area_rp_id`,
                SUM(IF(`evaluation_key_resource_people`.`stat` = 1, 1, 0)) AS `poor`,
                SUM(IF(`evaluation_key_resource_people`.`stat` = 2, 1, 0)) AS `fair`,
                SUM(IF(`evaluation_key_resource_people`.`stat` = 3, 1, 0)) AS `satisfactory`,
                SUM(IF(`evaluation_key_resource_people`.`stat` = 4, 1, 0)) AS `very_satisfactory`,
                SUM(IF(`evaluation_key_resource_people`.`stat` = 5, 1, 0)) AS `outstanding`,
                AVG(`evaluation_key_resource_people`.`stat`) AS `stat`,
                `training_resource_people`.`is_female` AS `is_female`,
                `training_resource_people`.`is_internal` AS `is_internal`,
                `training_resource_people`.`position` AS `position`,
                `training_resource_people`.`lname` AS `lname`,
                `training_resource_people`.`fname` AS `fname`,
                `training_resource_people`.`mname` AS `mname`,
                `training_resource_people`.`ext_name` AS `ext_name`
            FROM
                `evaluation_trainings`
            JOIN `evaluation_key_resource_people` ON
                `evaluation_trainings`.`id` = `evaluation_key_resource_people`.`evaluation_id`
            JOIN `training_resource_people` ON
                `evaluation_key_resource_people`.`rp_id` = `training_resource_people`.`id`
            WHERE
                `evaluation_trainings`.`deleted_at` IS NULL
            GROUP BY
                `evaluation_trainings`.`training_id`,
                `evaluation_key_resource_people`.`rp_id`,
                `evaluation_key_resource_people`.`area_rp_id`;

        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS `v2_evaluation_resource_person_view`');
    }
}
