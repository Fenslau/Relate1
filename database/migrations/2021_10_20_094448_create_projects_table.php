<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->integer('vkid');
            $table->string('project_name', 64);
            $table->string('rule', 128)->nullable()->index();

            $table->text('mode1')->nullable();
            $table->text('mode1_edit')->nullable();
            $table->mediumInteger('words1')->default(0);

            $table->text('mode2')->nullable();
            $table->text('mode2_edit')->nullable();
            $table->mediumInteger('words2')->default(16);

            $table->text('mode3')->nullable();
            $table->text('mode3_edit')->nullable();
            $table->mediumInteger('words3')->default(30);

            $table->text('minus_words')->nullable();
            $table->tinyInteger('old')->nullable();
            $table->tinyInteger('cut')->default(1);
            $table->tinyInteger('re_cloud')->default(0);

            $table->integer('mindate')->default(0);
            $table->integer('maxdate')->default(0);
            $table->text('countries')->nullable();
            $table->text('cities')->nullable();

            $table->integer('count_stream_records')->default(0);
            $table->text('ignore_authors')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
