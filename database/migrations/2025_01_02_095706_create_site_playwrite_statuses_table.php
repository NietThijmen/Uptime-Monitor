<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('site_playwrite_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_playwrite_id');
            $table->boolean('passes');
            $table->string('failed_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_playwrite_statuses');
    }
};
