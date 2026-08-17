<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'pcs', 'keterangan' => 'Pieces / Satuan buah'],
            ['nama' => 'rim', 'keterangan' => 'Satuan 500 lembar kertas'],
            ['nama' => 'botol', 'keterangan' => 'Satuan botol / kemasan cair'],
            ['nama' => 'pak', 'keterangan' => 'Satuan paket / kemasan'],
            ['nama' => 'roll', 'keterangan' => 'Satuan gulungan'],
            ['nama' => 'box', 'keterangan' => 'Satuan kotak / kardus'],
            ['nama' => 'unit', 'keterangan' => 'Satuan perangkat / alat'],
            ['nama' => 'meter', 'keterangan' => 'Satuan panjang'],
            ['nama' => 'lembar', 'keterangan' => 'Satuan per lembar'],
            ['nama' => 'lusin', 'keterangan' => 'Satuan 12 buah'],
            ['nama' => 'pack', 'keterangan' => 'Satuan pack'],
        ];

        foreach ($data as $item) {
            Satuan::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
