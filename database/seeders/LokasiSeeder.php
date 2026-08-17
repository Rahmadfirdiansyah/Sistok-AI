<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Meja DCO', 'deskripsi' => null],
            ['nama' => 'Data Hall 1', 'deskripsi' => null],
            ['nama' => 'GUDANG', 'deskripsi' => null],
            ['nama' => 'Lemari', 'deskripsi' => null],
            ['nama' => 'Ruko', 'deskripsi' => null],
         ['nama' => 'Tersimpan di lemari 1', 'deskripsi' => null],
            ['nama' => 'Rack A02 PT SAA', 'deskripsi' => null],
            ['nama' => 'PAC/DASEN-005/001', 'deskripsi' => null],
            ['nama' => 'PAC/DASEN-005/002', 'deskripsi' => null],
            ['nama' => 'PAC/DASEN-005/003', 'deskripsi' => null],
            ['nama' => 'PAC/DASEN-005/004', 'deskripsi' => null],
            ['nama' => 'PAC/DASEN-005/005', 'deskripsi' => null],
            ['nama' => 'Gudang Utama', 'deskripsi' => null],
            ['nama' => 'Lemari DCO', 'deskripsi' => null],
            ['nama' => 'Rak Fiber', 'deskripsi' => null],
        ];

        foreach ($data as $item) {
            Lokasi::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
