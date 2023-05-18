<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeIsFemale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluation_trainings', function(Blueprint $table){
            $table->string('sex', 10)->after('is_female')->default('0');
        });

        DB::table('evaluation_trainings')->where('is_female', true)
            ->update(['sex' => 'female']);

        DB::table('evaluation_trainings')->where('is_female', false)
            ->update(['sex' => 'male']);
        
        Schema::table('evaluation_trainings', function(Blueprint $table) {
            $table->dropColumn('is_female');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evaluation_trainings', function(Blueprint $table){
            $table->boolean('is_female')->after('sex')->default(false);
        });

        DB::table('evaluation_trainings')->where('sex', 'female')
            ->update(['is_female' => true]);

        DB::table('evaluation_trainings')
            ->whereIn('sex', ['male', '0'])
            ->update(['is_female' => false]);

        Schema::table('evaluation_trainings', function(Blueprint $table) {
            $table->dropColumn('sex');
        });
    }
}
