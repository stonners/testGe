<?php namespace BunkerPalace\Newsletter\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateNewslettersTable extends Migration
{
    public function up()
    {
        Schema::create('bunkerpalace_mcnewsletter_newsletters', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 255);
            $table->string('email_subject', 255);
            $table->text('body');
            $table->string('mc_list_id', 255);
            $table->integer('mc_segment_id')->unsigned()->default(0);
            $table->string('mc_campaign_id', 255);
            $table->string('mc_web_id', 255);
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bunkerpalace_mcnewsletter_newsletters');
    }
}
