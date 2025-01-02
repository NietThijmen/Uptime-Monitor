<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('site_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id');
            $table->boolean('up');
            $table->integer('status_code')->nullable();
            $table->float('response_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_statuses');
    }
};
