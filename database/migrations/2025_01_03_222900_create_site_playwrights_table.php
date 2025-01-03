<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_playwrights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id');
            $table->string('label');
            $table->longText('script');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_playwrights');
    }
};
