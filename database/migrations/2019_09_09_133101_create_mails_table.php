<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->default('queued');
            $table->integer('attempt')->default(0);
            $table->string('title');
            $table->string('fromName');
            $table->string('fromEmail');
            $table->json('to');
            $table->json('body_txt');
            $table->json('body_html');
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
        Schema::dropIfExists('mails');
    }
}
