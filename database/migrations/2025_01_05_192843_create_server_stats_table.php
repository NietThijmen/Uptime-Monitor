<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('server_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id');
            $table->double('total_cpu');
            $table->double('used_cpu');
            $table->double('total_memory');
            $table->double('used_memory');
            $table->json('disks');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_stats');
    }
};
