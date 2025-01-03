<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_playwright_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_playwright_id');
            $table->integer('batch');
            $table->boolean('passes');
            $table->string('failed_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_playwright_runs');
    }
};
