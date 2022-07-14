<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTop1000UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top1000_users', function (Blueprint $table) {
            $table->id();
            $table->enum('event_type', ['comment', 'post', 'reply', 'share', 'topic_post']);
            $table->integer('post_id')->index();
            $table->text('dublikat')->nullable();
            $table->text('event_url')->nullable();
            $table->integer('action_time')->nullable()->index();
            $table->text('video_player')->nullable();
            $table->text('photo')->nullable();
            $table->text('link')->nullable();
            $table->text('audio')->nullable();
            $table->text('doc')->nullable();
            $table->longText('note')->nullable();
            $table->integer('author_id')->index();
            $table->longText('data')->nullable();

            $table->integer('comments')->nullable();
            $table->integer('likes')->nullable();
            $table->integer('views')->nullable();
            $table->integer('reposts')->nullable();

            $table->timestamps();
            $table->unique(['author_id', 'post_id']);
        });
        DB::statement('ALTER TABLE top1000_users ADD FULLTEXT search(data)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('top1000_users', function($table) {
          $table->dropIndex('search');
        });
        Schema::dropIfExists('top1000_users');
    }
}
