<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_identification_type', function (Blueprint $table) {
            $table->bigIncrements('identification_type_id');
            $table->string('id', 20)->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_identification_type');
    }
};
