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
        if (!Schema::hasTable('attachments')) {
            // Create the table if it doesn't exist
            Schema::create('attachments', function (Blueprint $table) {
                $table->char('id', 36)->primary();
                $table->string('training_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('type');
                $table->softDeletes();
                $table->timestamps();
            });
        } else {
            // Modify the table if it exists
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

                if (!Schema::hasColumn('attachments', 'deleted_at')) {
                    $table->softDeletes();
                }

                if (!Schema::hasColumn('attachments', 'created_at') || !Schema::hasColumn('attachments', 'updated_at')) {
                    $table->timestamps();
                }
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
        Schema::dropIfExists('attachments');
    }
}
