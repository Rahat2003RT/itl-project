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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Внешний ключ на таблицу пользователей (users)
            $table->string('address_line_1'); // Адресная строка 1 (улица, дом)
            $table->string('address_line_2')->nullable(); // Дополнительная адресная строка (квартира, офис)
            $table->string('city');
            $table->string('state'); // Регион или область
            $table->string('postal_code');
            $table->string('country');
            $table->timestamps();
    
            // Определение внешнего ключа
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
