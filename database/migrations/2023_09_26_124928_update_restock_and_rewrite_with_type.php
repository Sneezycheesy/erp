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
        //
        Schema::table('write_offs', function(Blueprint $table) {
            $table->string('type')->default('write_off');
        });
        
        Schema::table('restocks', function(Blueprint $table) {
            $table->string('type')->default('restock');
            $table->unsignedInteger('new_stock')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('write_offs', function(Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('restocks', function(Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('new_stock');
        });
    }
};
