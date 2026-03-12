<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apk_detail', function (Blueprint $table) {
            $table->id('apk_detail_id')->unique()->autoIncrement();
            $table->string('original_filename');
            $table->string('new_filename');
            $table->date('uploaded_date');
            $table->text('description');
            $table->string('download_link');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apk_detail');
    }
};
