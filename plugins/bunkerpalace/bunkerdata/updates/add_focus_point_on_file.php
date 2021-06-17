<?php namespace TsumeArt\Core\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddFocusPointOnFile extends Migration
{
    public function up()
    {
        Schema::table('system_files', function(Blueprint $table) {
            $table->integer('bd_offset_x')->default(50);
            $table->integer('bd_offset_y')->default(50);
        });
    }

    public function down()
    {
        Schema::table('system_files', function (Blueprint $table) {
            $table->dropColumn('bd_offset_x');
            $table->dropColumn('bd_offset_y');
        });
    }
}
