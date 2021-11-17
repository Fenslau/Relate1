<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_data', function (Blueprint $table) {
            $table->id();
            $table->enum('event_type', ['comment', 'post', 'reply', 'share', 'topic_post']);
            $table->integer('post_id')->index();
            $table->tinyInteger('check_trash')->default(0)->index();
            $table->tinyInteger('check_flag')->default(0)->index();
            $table->tinyInteger('cloud')->default(0)->index();
            $table->string('user_links', 32)->nullable()->index();
            $table->text('dublikat')->nullable();
            $table->text('event_url')->nullable();
            $table->integer('shared_post_id')->nullable();
            $table->integer('action_time')->nullable()->index();
            $table->text('video_player')->nullable();
            $table->text('photo')->nullable();
            $table->text('link')->nullable();
            $table->text('audio')->nullable();
            $table->text('doc')->nullable();
            $table->longText('note')->nullable();
            $table->text('geo_type')->nullable();
            $table->text('geo_place_city')->nullable();
            $table->text('geo_place_country')->nullable();
            $table->text('geo_place_title')->nullable();
            $table->text('geo_place_icon')->nullable();
            $table->integer('author_id')->index();
            $table->integer('shared_post_author_id')->nullable();
            $table->enum('platform', ['Android','полная версия сайта','iPhone','мобильная версия','Windows Phone','iPad','сторонние приложения','Windows 8',''])->nullable();
            $table->string('user', 128)->index();
            $table->longText('data')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE stream_data ADD FULLTEXT search(data)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stream_data', function($table) {
          $table->dropIndex('search');
        });
        Schema::dropIfExists('stream_data');
    }
}
