<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('booking_type', 10)->default('member')->after('user_id');
            $table->string('guest_name')->nullable()->after('booking_type');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('guest_phone', 30)->nullable()->after('guest_email');
            $table->string('guest_identity_number', 50)->nullable()->after('guest_phone');
            $table->char('guest_gender', 1)->nullable()->after('guest_identity_number');
            $table->date('guest_birth_date')->nullable()->after('guest_gender');
            $table->text('guest_address')->nullable()->after('guest_birth_date');
            $table->string('access_token', 64)->unique()->after('notes');
            $table->unsignedInteger('subtotal')->default(0)->after('price_per_month');
            $table->unsignedInteger('discount')->default(0)->after('subtotal');
            $table->unsignedInteger('additional_fee')->default(0)->after('discount');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_type',
                'guest_name',
                'guest_email',
                'guest_phone',
                'guest_identity_number',
                'guest_gender',
                'guest_birth_date',
                'guest_address',
                'access_token',
            ]);
        });
    }
};
