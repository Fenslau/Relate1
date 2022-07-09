<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_users', function (Blueprint $table) {
            $table->id();
            $table->integer('vkid')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('screen_name')->nullable();
            $table->text('about')->nullable();
            $table->text('activities')->nullable();
            $table->text('interests')->nullable();
            $table->tinyInteger('sex')->nullable();
            $table->string('bdate')->nullable();
            $table->string('country')->index()->nullable();
            $table->string('city')->index()->nullable();
            $table->integer('followers_count')->nullable();
            $table->string('twitter')->nullable();
            $table->string('livejournal')->nullable();
            $table->string('skype')->nullable();
            $table->string('occupation')->nullable();
            $table->tinyInteger('relation')->nullable();
            $table->tinyInteger('verified')->nullable();
            $table->tinyInteger('is_closed')->nullable();
            $table->tinyInteger('can_post')->nullable();
            $table->tinyInteger('can_see_all_posts')->nullable();
            $table->tinyInteger('can_send_friend_request')->nullable();
            $table->tinyInteger('can_write_private_message')->nullable();
            $table->string('photo_100')->nullable();
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
        Schema::dropIfExists('top_users');
    }
}
