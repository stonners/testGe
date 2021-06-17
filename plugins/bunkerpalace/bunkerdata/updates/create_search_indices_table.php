<?php namespace BunkerPalace\BunkerData\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Db;

class CreateSearchIndicesTable extends Migration
{
    public function up()
    {
        Schema::create('search_indices', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('searchable_type', 255);
            $table->integer('searchable_id')->unsigned();
            $table->string('locale', 3);
            $table->string('title', 255);
            $table->longText('data');
            $table->index(['locale', 'searchable_type', 'searchable_id'], 'searchable');
        });

        Db::statement('ALTER TABLE search_indices ADD FULLTEXT INDEX index_data (title,data)');
    }

    public function down()
    {
        Schema::dropIfExists('search_indices');
    }
}
