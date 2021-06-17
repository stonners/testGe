<?php namespace BunkerPalace\TestGe\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateEventsTable extends Migration
{
    public function up()
    {
        Schema::table('bunkerpalace_testge_events', function(Blueprint $table) {
           $table->text('title');
         });
    }

    public function down()
    {
        Schema::dropIfExists('bunkerpalace_testge_events');
    }
}
