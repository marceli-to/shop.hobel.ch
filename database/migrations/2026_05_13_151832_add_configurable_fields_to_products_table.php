<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('min_length', 8, 2)->nullable()->after('stock');
            $table->decimal('max_length', 8, 2)->nullable()->after('min_length');
            $table->decimal('min_width', 8, 2)->nullable()->after('max_length');
            $table->decimal('max_width', 8, 2)->nullable()->after('min_width');
            $table->decimal('base_price', 10, 2)->nullable()->after('max_width');
            $table->decimal('material_factor', 8, 2)->nullable()->after('base_price');
            $table->decimal('surface_processing_price', 10, 2)->nullable()->after('material_factor');
            $table->decimal('large_format_threshold', 8, 2)->nullable()->after('surface_processing_price');
            $table->decimal('large_format_surcharge', 10, 2)->nullable()->after('large_format_threshold');
            $table->decimal('oversize_threshold', 8, 2)->nullable()->after('large_format_surcharge');
            $table->decimal('oversize_surcharge', 10, 2)->nullable()->after('oversize_threshold');
            $table->decimal('minimum_price', 10, 2)->nullable()->after('oversize_surcharge');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'min_length',
                'max_length',
                'min_width',
                'max_width',
                'base_price',
                'material_factor',
                'surface_processing_price',
                'large_format_threshold',
                'large_format_surcharge',
                'oversize_threshold',
                'oversize_surcharge',
                'minimum_price',
            ]);
        });
    }
};
