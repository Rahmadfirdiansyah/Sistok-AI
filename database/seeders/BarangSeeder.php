<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Supplier;
use App\Models\Satuan;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = [
            // FIBER OPTIC
            ['kode' => 'BRG-0001', 'nama' => 'SFP NOKIA 10G 1,4KM',                                             'kategori' => 'Fiber Optic',  'lokasi' => 'Gudang Utama', 'satuan' => 'pcs',   'stok' => 3,  'min_stok' => 10],
            ['kode' => 'BRG-0002', 'nama' => 'SFP HUAWEI 10G 1,4KM',                                            'kategori' => 'Fiber Optic',  'lokasi' => 'Gudang Utama', 'satuan' => 'pcs',   'stok' => 2,  'min_stok' => 5],
            ['kode' => 'BRG-0003', 'nama' => 'SFP HUAWEI 1,25G 10KM',                                           'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 8,  'min_stok' => 6],
            ['kode' => 'BRG-0004', 'nama' => 'SFP HUAWEI 10G 10KM',                                             'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 6,  'min_stok' => 5],
            ['kode' => 'BRG-0005', 'nama' => 'PATCHCORE LC - LC 1,5 METER',                                     'kategori' => 'Fiber Optic',  'lokasi' => 'Rak Fiber',    'satuan' => 'pcs',   'stok' => 24, 'min_stok' => 10],
            ['kode' => 'BRG-0006', 'nama' => 'PATCHCORE LC - LC 2 METER',                                       'kategori' => 'Fiber Optic',  'lokasi' => 'Gudang Utama', 'satuan' => 'pcs',   'stok' => 15, 'min_stok' => 5],
            ['kode' => 'BRG-0007', 'nama' => 'PATCHCORE LC - LC Duplex 3 METER',                                'kategori' => 'Fiber Optic',  'lokasi' => 'Gudang Utama', 'satuan' => 'pcs',   'stok' => 30, 'min_stok' => 10],
            ['kode' => 'BRG-0008', 'nama' => 'PATCHCORE LC - LC Duplex 5 METER',                                'kategori' => 'Fiber Optic',  'lokasi' => 'Gudang Utama', 'satuan' => 'pcs',   'stok' => 20, 'min_stok' => 10],
            ['kode' => 'BRG-0009', 'nama' => 'PATCHCORE LC - LC Duplex  10 METER',                              'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0010', 'nama' => 'PATCHCORE LC - LC Duplex 15 METER',                               'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0011', 'nama' => 'PATCHCORE LC - LC Duplex 20 METER',                               'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0012', 'nama' => 'PATCHCORE LC - LC Duplex 25 METER',                               'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0013', 'nama' => 'Mikrobits QSFP + Transceiver 40G 1310nm 10km Singlemode',         'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0014', 'nama' => 'Connector SC To LC UPC Adapter | Fiber Optic LC Female to SC Male', 'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0015', 'nama' => 'PATCHCORE SC - SC 1 core 1,5 METER',                              'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0016', 'nama' => 'PATCHCORE SC - SC Duplex 3 METER',                                'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0017', 'nama' => 'PATCHCORE SC - SC 5 M',                                           'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0018', 'nama' => 'Connector LC to SC',                                              'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0019', 'nama' => 'Laser Fiber Optic',                                               'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'unit',  'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0020', 'nama' => 'OPM ( OPTICAL POWER METER )',                                     'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'unit',  'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0021', 'nama' => 'Kaset FO OTB',                                                    'kategori' => 'Fiber Optic',  'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
        
            // NETWORKING
            ['kode' => 'BRG-0022', 'nama' => 'Konektor RJ45 Vention Cat 6 UTP Modular Gigabit Connector',       'kategori' => 'Networking',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0023', 'nama' => 'POE Splitter USB-C 48V to 5V Adaptor Power',                      'kategori' => 'Networking',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0024', 'nama' => 'Kabel USB  To LAN RJ45 Console',                                  'kategori' => 'Networking',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0025', 'nama' => 'Kabel Serial DB9 Female To LAN RJ45 1.5M Console',                'kategori' => 'Networking',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0026', 'nama' => 'Connector USB To LAN Female',                                     'kategori' => 'Networking',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0027', 'nama' => 'Lan tester / Wiretracker',                                        'kategori' => 'Networking',   'lokasi' => 'Lemari DCO',   'satuan' => 'unit',  'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0028', 'nama' => 'Plug boot Karet Pelindung RJ45 isi 50pcs warna Biru',             'kategori' => 'Networking',   'lokasi' => 'Lemari DCO',   'satuan' => 'pack',  'stok' => 50, 'min_stok' => 20],
        
            // KABEL
            ['kode' => 'BRG-0029', 'nama' => 'KABEL POWER C13-C14',                                             'kategori' => 'Kabel',        'lokasi' => 'Rak Fiber',    'satuan' => 'pcs',   'stok' => 4,  'min_stok' => 3],
            ['kode' => 'BRG-0030', 'nama' => 'Vention Kabel HDMI Cable 3D UHD 4K 60Hz 30Hz 1080P PVC Material 2M', 'kategori' => 'Kabel',     'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0031', 'nama' => 'Kabel Power Printer',                                             'kategori' => 'Kabel',        'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
        
            // ELEKTRIKAL
            ['kode' => 'BRG-0032', 'nama' => 'MCB SCHNEIDER 6 A, 10 A, 16 A, 32 A, 50 A',                       'kategori' => 'Elektrikal',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0033', 'nama' => 'Konverter c14 to PDU',                                            'kategori' => 'Elektrikal',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0034', 'nama' => 'STOP KONTAK + KABEL 5 LUBANG PANJANG 5 METER',                    'kategori' => 'Elektrikal',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0035', 'nama' => 'Wan Dao - Socket Plug Mounting Cabang 2 16A 3pin',                'kategori' => 'Elektrikal',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
        
            // PERKAKAS
            ['kode' => 'BRG-0036', 'nama' => 'Tang Kremping',                                                   'kategori' => 'Perkakas',     'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0037', 'nama' => 'Tangga Rack',                                                     'kategori' => 'Perkakas',     'lokasi' => 'Lemari DCO',   'satuan' => 'unit',  'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0038', 'nama' => 'Obeng Berkemagnet',                                               'kategori' => 'Perkakas',     'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
        
            // HARDWARE
            ['kode' => 'BRG-0039', 'nama' => 'Jalur Kabel Data Hall / Kotak sikat Jalur Kabel/ Grommet',        'kategori' => 'Hardware',     'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0040', 'nama' => 'Kabel Protektor 1,7 Meter , 5 cm x 5 cm / Cable Duct / Kabel Dak', 'kategori' => 'Hardware',    'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0041', 'nama' => 'HPP Paku Beton 250gr - Paku Cor Baja Tembok - Concrete Nail 1 1/2 inchi', 'kategori' => 'Hardware', 'lokasi' => 'Lemari DCO',   'satuan' => 'box',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0042', 'nama' => 'Dynabolt Besi 10 x 65 / Dynabolt M10X65 / Dinabolt Baut Beton',   'kategori' => 'Hardware',     'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
        
            // ATK
            ['kode' => 'BRG-0043', 'nama' => 'Map Clear Holder Folio Eselon 40 Lembar',                         'kategori' => 'ATK',          'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0044', 'nama' => '[50 Pcs / 1 Pack] Hang Map File - NIKIKO - Hijau',                'kategori' => 'ATK',          'lokasi' => 'Lemari DCO',   'satuan' => 'pack',  'stok' => 50, 'min_stok' => 20],
        
            // CONSUMABLE
            ['kode' => 'BRG-0045', 'nama' => 'Double Tape 3M Putih Foam Double Tape Putih',                     'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'roll',  'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0046', 'nama' => 'Lakban 200 Yard Coklat/Bening Grosir Promo15M - AAC',             'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'roll',  'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0047', 'nama' => 'Wallpaper Meja Motif Kayu 4m x 45cm',                             'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'roll',  'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0048', 'nama' => 'Tape Label TZe231 Brother 12mm Hitam Putih',                      'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0049', 'nama' => 'Tape Label TZe231 Brother 12mm Hitam Kuning',                     'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0050', 'nama' => 'Tape Label TZe231 Brother 12mm Hitam Merah',                      'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0051', 'nama' => 'Tape Label TZe231 Brother 12mm Hitam Emas',                       'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0052', 'nama' => 'Baterai AA / AAA Alkaline',                                       'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0053', 'nama' => 'Baterai AA Sony 4600mAh Rechargeable/Isi Ulang',                  'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0054', 'nama' => 'Isolasi Listrik National PVC Electrical Tape 15mm x 20Yard SNI',  'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'roll',  'stok' => 50, 'min_stok' => 20],
            ['kode' => 'BRG-0055', 'nama' => '1000ml Air Aki Yuasa Tutup Biru',                                 'kategori' => 'Consumable',   'lokasi' => 'Lemari DCO',   'satuan' => 'botol', 'stok' => 50, 'min_stok' => 20],
        
            // KEAMANAN
            ['kode' => 'BRG-0056', 'nama' => 'Anti Slip Tape Hitam 5M Safety Walk lantai tangga',               'kategori' => 'Keamanan',     'lokasi' => 'Lemari DCO',   'satuan' => 'roll',  'stok' => 50, 'min_stok' => 20],
        
            // LAINNYA
            ['kode' => 'BRG-0057', 'nama' => 'Micro SD',                                                        'kategori' => 'Lainnya',      'lokasi' => 'Lemari DCO',   'satuan' => 'pcs',   'stok' => 50, 'min_stok' => 20],
        ];
 
        foreach ($barangs as $b) {
            $kategoriId = Kategori::where('nama', $b['kategori'])->value('id');
            $lokasiId = Lokasi::where('nama', $b['lokasi'])->value('id');
            $satuanId = Satuan::where('nama', $b['satuan'])->value('id');
 
            if ($kategoriId && $lokasiId && $satuanId) {
                Barang::firstOrCreate(
                    ['kode' => $b['kode']],
                    [
                        'nama' => $b['nama'],
                        'kategori_id' => $kategoriId,
                        'lokasi_id' => $lokasiId,
                        'satuan_id' => $satuanId,
                        'stok' => $b['stok'],
                        'min_stok' => $b['min_stok'],
                    ]
                );
            }
        }
    }
}
 