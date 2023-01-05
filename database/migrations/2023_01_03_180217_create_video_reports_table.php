<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('report_category_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('video_id');
            $table->text('info');
            $table->unsignedSmallInteger('first_time')->nullable();
            $table->unsignedSmallInteger('second_time')->nullable();
            $table->unsignedSmallInteger('third_time')->nullable();
            $table->timestamps();

            $table->foreign('report_category_id')
                ->references('id')
                ->on('video_report_categories')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->foreign('video_id')
                ->references('id')
                ->on('videos')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_reports');
    }
}
