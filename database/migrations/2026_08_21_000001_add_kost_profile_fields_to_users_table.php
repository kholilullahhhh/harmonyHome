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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('avatar');
            $table->string('gender', 1)->nullable()->after('phone'); // L | P
            $table->date('birthdate')->nullable()->after('gender');
            $table->text('address')->nullable()->after('birthdate');
            $table->string('identity_number', 30)->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'gender', 'birthdate', 'address', 'identity_number']);
        });
    }
};
