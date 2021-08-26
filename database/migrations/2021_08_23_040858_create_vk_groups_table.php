<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVkGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vk_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id')->unique();
            $table->string('name');
            $table->string('city', 64);
            $table->integer('members_count');
            $table->enum('type', ['group', 'page', 'event']);
            $table->smallInteger('wall');
            $table->string('site', 128);
            $table->tinyInteger('verified');
            $table->tinyInteger('market');
            $table->tinyInteger('is_closed');
            $table->text('contacts');
            $table->string('public_date_label', 32)->nullable();
            $table->string('start_date', 32)->nullable();
            $table->string('finish_date', 32)->nullable();
            $table->text('tags');
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
        Schema::dropIfExists('vk_groups');
    }
}
