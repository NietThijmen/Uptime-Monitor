<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('site_lighthouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id');
            $table->integer('performance');
            $table->integer('accessibility');
            $table->integer('best_practices');
            $table->integer('seo');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_lighthouses');
    }
};
