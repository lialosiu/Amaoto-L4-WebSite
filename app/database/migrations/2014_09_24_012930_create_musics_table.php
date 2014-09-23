<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->index();
            $table->string('artist')->index();
            $table->string('year')->index();
            $table->integer('track');
            $table->string('genre')->index();
            $table->string('mime_type');
            $table->double('playtime_seconds');
            $table->string('playtime_string');
            $table->double('bitrate');
            $table->string('tag_title');
            $table->string('tag_artist');
            $table->string('tag_album');
            $table->string('tag_year');
            $table->string('tag_track');
            $table->string('tag_genre');
            $table->string('tag_comment');
            $table->string('tag_album_artist');
            $table->string('tag_composer');
            $table->string('tag_disc_number');
            $table->text('tag_json');
            $table->bigInteger('file_id')->index();
            $table->bigInteger('album_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('musics');
    }

}
