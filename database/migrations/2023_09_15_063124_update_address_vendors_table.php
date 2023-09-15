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
        Schema::table('vendors', function (Blueprint $table) {
            $table->text('zipcode');
            $table->text('city');
            $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('zipcode');
            $table->dropColumn('city');
            $table->dropColumn('country');
        });
    }
};
