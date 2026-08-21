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
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kost_id')->constrained('kosts')->cascadeOnDelete();
            $table->foreignId('tipe_kamar_id')->nullable()->constrained('tipe_kamars')->nullOnDelete();
            $table->string('number', 20);
            $table->integer('price_monthly'); // Rupiah, tanpa sen
            $table->string('size', 20)->nullable(); // contoh: 3x4
            $table->unsignedTinyInteger('floor')->nullable();
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->string('status', 20)->default('available')->index(); // available | reserved | occupied | maintenance
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['kost_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
