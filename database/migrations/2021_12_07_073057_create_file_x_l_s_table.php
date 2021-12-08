<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileXLSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_x_l_s', function (Blueprint $table) {
            $table->id();
            $table->integer('vkid');
            $table->text('project_name')->nullable();
            $table->text('get_params')->nullable();
            $table->text('link')->nullable();
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
        Schema::dropIfExists('file_x_l_s');
    }
}
