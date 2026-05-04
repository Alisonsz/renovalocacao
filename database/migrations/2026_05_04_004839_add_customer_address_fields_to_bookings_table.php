<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('customer_zip_code')->nullable()->after('customer_company');
            $table->string('customer_street')->nullable()->after('customer_city');
            $table->string('customer_number', 10)->nullable()->after('customer_street');
            $table->string('customer_reference')->nullable()->after('customer_number');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'customer_zip_code',
                'customer_street',
                'customer_number',
                'customer_reference',
            ]);
        });
    }
};
