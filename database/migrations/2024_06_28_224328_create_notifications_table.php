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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID пользователя, который получит уведомление
            $table->string('type'); // Тип уведомления
            $table->text('data'); // Данные уведомления
            $table->timestamp('read_at')->nullable(); // Время прочтения уведомления
            $table->timestamps(); // created_at и updated_at

            // Внешний ключ для связи с таблицей пользователей
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
