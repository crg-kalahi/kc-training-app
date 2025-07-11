<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            if (!Schema::hasColumn('attachments', 'id')) {
                $table->char('id', 36)->primary();
            }

            if (!Schema::hasColumn('attachments', 'training_id')) {
                $table->string('training_id');
            }

            if (!Schema::hasColumn('attachments', 'name')) {
                $table->string('name');
            }

            if (!Schema::hasColumn('attachments', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('attachments', 'type')) {
                $table->string('type');
            }

            // softDeletes and timestamps are special — adding them twice may cause problems
            // So check for 'deleted_at' column to add softDeletes
            if (!Schema::hasColumn('attachments', 'deleted_at')) {
                $table->softDeletes();
            }

            // Check if timestamps columns exist
            if (!Schema::hasColumn('attachments', 'created_at') && !Schema::hasColumn('attachments', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        // Usually you only drop columns you added in `up()`
        Schema::table('attachments', function (Blueprint $table) {
            if (Schema::hasColumn('attachments', 'id')) {
                $table->dropColumn('id');
            }
            if (Schema::hasColumn('attachments', 'training_id')) {
                $table->dropColumn('training_id');
            }
            if (Schema::hasColumn('attachments', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('attachments', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('attachments', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('attachments', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('attachments', 'created_at') && Schema::hasColumn('attachments', 'updated_at')) {
                $table->dropTimestamps();
            }
        });
    }
}
