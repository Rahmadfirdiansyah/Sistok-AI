<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
    [
        'nama' => 'Fiber Optic',
        'deskripsi' => 'Perangkat dan aksesoris jaringan fiber optik seperti SFP, patchcord, adapter, OTB, OPM, dan splitter.'
    ],
    [
        'nama' => 'Networking',
        'deskripsi' => 'Perangkat jaringan dan aksesorinya seperti RJ45, console cable, LAN tester, switch, router, dan POE.'
    ],
    [
        'nama' => 'Elektrikal',
        'deskripsi' => 'Peralatan kelistrikan seperti MCB, stop kontak, kabel power, adaptor, dan konektor listrik.'
    ],
    [
        'nama' => 'Perkakas',
        'deskripsi' => 'Peralatan kerja seperti tang crimping, obeng, tangga, dan toolkit.'
    ],
    [
        'nama' => 'ATK',
        'deskripsi' => 'Alat tulis kantor dan perlengkapan administrasi.'
    ],
    [
        'nama' => 'Kabel',
        'deskripsi' => 'Berbagai jenis kabel data, power, HDMI, USB, serial, dan aksesorinya.'
    ],
    [
        'nama' => 'Hardware',
        'deskripsi' => 'Material instalasi seperti dynabolt, paku beton, cable duct, grommet, dan bracket.'
    ],
    [
        'nama' => 'Consumable',
        'deskripsi' => 'Barang habis pakai seperti baterai, lakban, isolasi, double tape, label tape, dan air aki.'
    ],
    [
        'nama' => 'Furniture',
        'deskripsi' => 'Perabot dan perlengkapan ruangan seperti meja, kursi, lemari, dan rak.'
    ],
    [
        'nama' => 'Kebersihan',
        'deskripsi' => 'Peralatan dan bahan kebersihan kantor maupun data center.'
    ],
    [
        'nama' => 'Keamanan',
        'deskripsi' => 'Perlengkapan keselamatan kerja dan keamanan seperti anti slip tape, APAR, helm, dan rambu.'
    ],
    [
        'nama' => 'Lainnya',
        'deskripsi' => 'Barang yang belum termasuk dalam kategori lain.'
    ],
        ];

        foreach ($data as $item) {
            Kategori::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
