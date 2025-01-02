<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('site_playwrites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id');
            $table->longText('script_content');
            $table->enum('run_at', [
                'hourly',
                'twice_daily',
                'daily',
                'weekly',
            ])->default('hourly');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_playwrites');
    }
};