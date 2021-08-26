<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOplatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oplatas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('demo', 32)->nullable();
            $table->integer('vkid');
            $table->string('bill_id', 64)->nullable();
            $table->integer('old_post_limit')->nullable();
            $table->integer('old_post_fact')->nullable();
            $table->integer('project_limit')->nullable();
            $table->integer('rules_limit')->nullable();
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
        Schema::dropIfExists('oplatas');
    }
}
