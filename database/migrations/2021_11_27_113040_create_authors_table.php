<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->integer('author_id')->unique();
            $table->mediumText('name')->nullable();
            $table->string('country', 64)->nullable()->index();
            $table->string('city', 128)->nullable()->index();
            $table->integer('members_count')->nullable()->index();
            $table->integer('city_id')->nullable()->index();
            $table->tinyInteger('sex')->nullable();
            $table->smallInteger('age')->nullable()->index();          
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
        Schema::dropIfExists('authors');
    }
}
