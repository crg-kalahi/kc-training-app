<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilePathToAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
        {
            // Check if 'file_path' column does NOT exist before adding it
            if (!Schema::hasColumn('attachments', 'file_path')) {
                Schema::table('attachments', function (Blueprint $table) {
                    $table->string('file_path')->nullable();
                });
            }
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            // Check if 'file_path' column exists before dropping it
            if (Schema::hasColumn('attachments', 'file_path')) {
                Schema::table('attachments', function (Blueprint $table) {
                    $table->dropColumn('file_path');
                });
            }
        }
}
