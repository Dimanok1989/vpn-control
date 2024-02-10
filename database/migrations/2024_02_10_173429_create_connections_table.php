<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ipsec_connections', function (Blueprint $table) {
            $table->id();
            $table->string('user')->nullable();
            $table->string('verb')->nullable();
            $table->string('connect')->nullable();
            $table->unsignedBigInteger('uniqueid')->nullable();
            $table->ipAddress('peer')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipsec_connections');
    }
};
