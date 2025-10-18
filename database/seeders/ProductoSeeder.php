<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $nombres = [
            'Laptop Lenovo', 'Mouse Logitech', 'Teclado Redragon', 'Monitor Samsung',
            'Impresora HP', 'Smartphone Xiaomi', 'Tablet Huawei', 'Auriculares JBL',
            'Cámara Canon', 'Disco SSD Kingston', 'Memoria RAM Corsair', 'Silla gamer',
            'Router TP-Link', 'Power Bank Anker', 'Webcam Logitech', 'Altavoz Bluetooth',
            'Tarjeta gráfica NVIDIA', 'Microprocesador AMD Ryzen', 'Micrófono Blue Yeti', 'Cargador USB-C Baseus',
        ];

        foreach ($nombres as $nombre) {
            Producto::create([
                'nombre' => $nombre,
                'precio' => rand(20, 500),
                'descripcion' => 'Producto de alta calidad: ' . $nombre,
            ]);
        }
    }
}
