<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('confirmation_email_sent')->default(false)->after('paid_at');
            $table->boolean('admin_email_sent')->default(false)->after('confirmation_email_sent');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['confirmation_email_sent', 'admin_email_sent']);
        });
    }
};
