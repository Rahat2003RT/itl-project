<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('users')->insert([
            'name' => 'Рахат',
            'email' => 'rahat.turmyshov@mail.ru',
            'email_verified_at' => now(), // Устанавливаем верифицированный email
            'password' => Hash::make('123'), // Хэшируем пароль
            'delivery_address' => 'УЛ Солнечная',
            'role' => 'admin', // Устанавливаем роль admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
