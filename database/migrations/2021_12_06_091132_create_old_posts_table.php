<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOldPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('vkid');
            $table->string('token')->nullable();
            $table->string('user', 128)->nullable();
            $table->integer('start_date')->nullable();
            $table->integer('end_date')->nullable();
            $table->integer('last_date')->nullable();
            $table->integer('retry')->nullable();
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
        Schema::dropIfExists('old_posts');
    }
}
