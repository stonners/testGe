<?php namespace BunkerPalace\BunkerData\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateVideosTable extends Migration
{
    public function up()
    {
        Schema::create('system_videos', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->string('disk_name', 255);
            $table->string('title', 255);
            $table->string('url', 255);
            $table->string('provider_name', 255);
            $table->string('width', 255);
            $table->string('height', 255);
            $table->string('thumbnail_url', 255);
            $table->string('thumbnail_width', 255);
            $table->string('thumbnail_height', 255);
            $table->string('html', 255);

            $table->string('field', 255)->nullable();
            $table->string('attachment_id', 255)->nullable();
            $table->string('attachment_type', 255)->nullable();

            $table->boolean('is_public')->default(1);
            $table->integer('sort_order')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_videos');
    }
}
