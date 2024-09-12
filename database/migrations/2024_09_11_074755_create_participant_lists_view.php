<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantListsView extends Migration
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
            ALGORITHM = UNDEFINED VIEW `participant_lists_view` AS
            SELECT
                `training_participants`.`is_internal` AS `is_internal`,
                `training_participants`.`email` AS `email`,
                `training_participants`.`is_female` AS `is_female`,
                `training_participants`.`position` AS `position`,
                `training_participants`.`lname` AS `lname`,
                `training_participants`.`fname` AS `fname`,
                `training_participants`.`mname` AS `mname`,
                `training_participants`.`ext_name` AS `ext_name`,
                `training_participants`.`id` AS `id`,
                CONCAT(`training_participants`.`lname`, ', ', `training_participants`.`fname`) AS `full_name`
            FROM
                `training_participants`
            GROUP BY
                CONCAT(`training_participants`.`lname`, ', ', `training_participants`.`fname`),
                `training_participants`.`is_internal`
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS `participant_lists_view`');
    }
}
