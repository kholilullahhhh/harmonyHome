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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // penyewa
            $table->foreignId('kost_id')->constrained('kosts')->cascadeOnDelete();
            $table->foreignId('kamar_id')->constrained('kamar')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('duration_months');
            $table->integer('price_per_month'); // snapshot harga saat booking
            $table->integer('total_amount');
            $table->string('status', 20)->default('pending')->index(); // pending | confirmed | active | completed | cancelled | rejected
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['kamar_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
