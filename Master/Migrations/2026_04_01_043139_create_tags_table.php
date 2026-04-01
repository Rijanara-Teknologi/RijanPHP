<?php

use Teguh02\Rijanphp\Core\Database\Migration\Migration;
use Teguh02\Rijanphp\Core\Database\Schema\Blueprint;
use Teguh02\Rijanphp\Core\Database\Schema\Schema;

class CreateTagsTable extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tags');
    }
}