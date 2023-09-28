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
        Schema::table('write_offs', function (Blueprint $table) {
            $table->unsignedInteger('new_stock')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('write_offs', function (Blueprint $table) {
            //
            $table->dropColumn('new_stock');
        });
    }
};