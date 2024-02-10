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
            $table->tinyInteger('day')->nullable()->after('created_at');
            $table->tinyInteger('month')->nullable()->after('day');
            $table->year('year')->nullable()->after('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ipsec_connections', function (Blueprint $table) {
            $table->dropColumn([
                'year',
                'month',
                'day',
            ]);
        });
    }
};
