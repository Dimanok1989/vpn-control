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
        Schema::table('ipsec_connections', function (Blueprint $table) {
            $table->unsignedBigInteger('reqid')->nullable()->after('uniqueid');
            $table->unsignedBigInteger('bytes_in')->nullable()->after('ip');
            $table->unsignedBigInteger('bytes_out')->nullable()->after('bytes_in');
            $table->unsignedBigInteger('seconds')->nullable()->after('bytes_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ipsec_connections', function (Blueprint $table) {
            $table->dropColumn([
                'reqid',
                'bytes_in',
                'bytes_out',
                'seconds',
            ]);
        });
    }
};
