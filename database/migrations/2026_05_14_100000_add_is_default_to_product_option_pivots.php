<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edge_product', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('product_id');
        });

        Schema::table('product_surface', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('surface_id');
        });

        Schema::table('product_wood_type', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('wood_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('edge_product', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });

        Schema::table('product_surface', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });

        Schema::table('product_wood_type', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
