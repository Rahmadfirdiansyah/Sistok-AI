<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Kategori;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTransactionController extends Controller
{
    public function parseChat(Request $request)
    {
        $rawText = trim($request->input('text', ''));
        $lowerText = strtolower($rawText);

        // Easter egg
        if (strpos($lowerText, 'rahmad') !== false) {
            return response()->json([
                'success' => false,
                'is_easter_egg' => true,
                'message' => 'Mas Rahmad? Dia adalah seorang yang sangat baik, tampan, dan programmer yang hebat! 😎✨'
            ]);
        }

        // Try Gemini API first if configured
        $geminiResult = $this->parseWithGemini($rawText);

        if ($geminiResult && isset($geminiResult['intent'])) {
            return $this->handleGeminiResponse($geminiResult, $rawText);
        }

        // Fallback to local Regex parsing engine if Gemini API key is missing
        return $this->parseWithRegex($rawText, $lowerText);
    }

    /**
     * Integrasi Google AI Studio (Gemini API) dengan Multi-Model Fallback & Context Injection
     */
    protected function parseWithGemini(string $rawText): ?array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return null;
        }

        $models = array_values(array_unique(array_filter([
            env('GEMINI_MODEL', 'gemini-3.6-flash'),
            'gemini-3.6-flash',
            'gemini-flash-latest'
        ])));

        // Context Preparation dari Database
        $barangs = Barang::with(['kategori', 'lokasi', 'satuan'])->get()->map(function ($b) {
            return [
                'id' => $b->id,
                'nama' => $b->nama,
                'kode' => $b->kode,
                'stok' => $b->stok,
                'satuan' => $b->satuan ? $b->satuan->nama : 'unit',
                'lokasi' => $b->lokasi ? $b->lokasi->nama : '',
                'kategori' => $b->kategori ? $b->kategori->nama : ''
            ];
        });

        $kategoris = Kategori::all(['id', 'nama']);
        $lokasis = Lokasi::all(['id', 'nama']);

        $systemPrompt = "Kamu adalah Sistok AI, asisten manajemen stok gudang pintar untuk sistem inventaris perusahaan.
Tugasmu adalah menganalisis pesan pengguna dan menentukan aksinya sesuai database inventaris terkini.

DAFTAR BARANG DI DATABASE SAAT INI:
" . json_encode($barangs, JSON_UNESCAPED_UNICODE) . "

DAFTAR KATEGORI:
" . json_encode($kategoris, JSON_UNESCAPED_UNICODE) . "

DAFTAR LOKASI:
" . json_encode($lokasis, JSON_UNESCAPED_UNICODE) . "

FORMAT RESPON:
Kamu HARUS selalu menjawab dengan JSON murni tanpa pembungkus markdown. Skema JSON berdasarkan intent:

1. 'masuk': Penambahan/retur/pengembalian stok barang.
   Schema: {\"intent\": \"masuk\", \"barang_id\": <number>, \"qty\": <number>, \"keterangan\": <string>}
2. 'keluar': Pengurangan/pemakaian stok barang.
   Schema: {\"intent\": \"keluar\", \"barang_id\": <number>, \"qty\": <number>, \"dipakai_oleh\": <string>, \"tujuan\": <string>, \"keterangan\": <string>}
3. 'info': Menanyakan sisa stok barang tertentu.
   Schema: {\"intent\": \"info\", \"barang_id\": <number>}
4. 'new_item': Pendaftaran barang baru yang jelas BELUM ada di daftar barang saat ini.
   Schema: {\"intent\": \"new_item\", \"nama_barang\": <string>, \"kategori_id\": <number>, \"lokasi_id\": <number>}
5. 'new_kategori': Pendaftaran kategori master baru (contoh: 'tambah kategori Elektronik').
   Schema: {\"intent\": \"new_kategori\", \"nama\": <string>, \"deskripsi\": <string>}
6. 'new_lokasi': Pendaftaran lokasi master baru (contoh: 'tambah lokasi Rak C-5').
   Schema: {\"intent\": \"new_lokasi\", \"nama\": <string>, \"deskripsi\": <string>}
7. 'new_satuan': Pendaftaran satuan master baru (contoh: 'tambah satuan Box' atau 'tambah satuan Roll').
   Schema: {\"intent\": \"new_satuan\", \"nama\": <string>, \"keterangan\": <string>}
8. 'undo': Membatalkan transaksi terakhir.
   Schema: {\"intent\": \"undo\"}
9. 'rekap': Laporan/rekap transaksi hari ini.
   Schema: {\"intent\": \"rekap\"}
10. 'stok_kritis': Meminta daftar barang dengan stok kritis / habis / menipis.
   Schema: {\"intent\": \"stok_kritis\"}
11. 'chat': Pertanyaan umum, percakapan, saran, analisis stok, atau jika pengguna mencari/menanyakan barang berdasarkan fungsi/kegunaan/deskripsi.
   Schema: {\"intent\": \"chat\", \"message\": <string dalam format HTML ramah Bahasa Indonesia (gunakan <b>, <br>, <i>, <ul>, <li>)>}

- Jika pengguna meminta 'tambah kategori <nama>' atau 'bikin kategori <nama>', gunakan intent 'new_kategori' dengan nama yang diekstrak.
- Jika pengguna meminta 'tambah lokasi <nama>' atau 'bikin lokasi <nama>', gunakan intent 'new_lokasi' dengan nama yang diekstrak.
- Jika pengguna meminta 'tambah satuan <nama>' atau 'bikin satuan <nama>', gunakan intent 'new_satuan' dengan nama yang diekstrak.
- Jika pengguna menanyakan barang berdasarkan kegunaannya (contoh: 'barang buat isi aki genset'), cari barang terdekat di DAFTAR BARANG (misalnya Air Aki Yuasa), gunakan intent 'chat', dan berikan saran barang yang cocok lengkap dengan kode, sisa stok, serta lokasinya.
- Selalu utamakan mencocokkan nama barang dari pesan pengguna dengan 'id' barang yang ada di DAFTAR BARANG.
- Jika intent 'keluar', ekstrak siapa yang memakai ('dipakai_oleh') dan tujuannya ('tujuan') jika ada di teks pengguna.";

        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            try {
                $response = Http::timeout(10)->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nPesan Pengguna: " . $rawText]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.2
                    ]
                ]);

                if ($response->successful()) {
                    $jsonResponse = $response->json();
                    $responseText = $jsonResponse['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $responseText = preg_replace('/^```json\s*|\s*```$/i', '', trim($responseText));

                    $data = json_decode($responseText, true);
                    if ($data && isset($data['intent'])) {
                        return $data;
                    }
                }
                Log::warning("Gemini API model {$model} failed: " . $response->body());
            } catch (\Exception $e) {
                Log::error("Gemini API Exception on model {$model}: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Memproses respon JSON dari Gemini API
     */
    protected function handleGeminiResponse(array $res, string $rawText)
    {
        $intent = $res['intent'] ?? 'chat';

        // Intent: Undo
        if ($intent === 'undo') {
            return $this->executeUndo();
        }

        // Intent: Rekap
        if ($intent === 'rekap') {
            return $this->executeRekap();
        }

        // Intent: Stok Kritis
        if ($intent === 'stok_kritis') {
            return $this->executeStokKritis();
        }

        // Intent: Chat / General Q&A / Rekomendasi Barang
        if ($intent === 'chat') {
            $regexResult = $this->parseWithRegex($rawText, strtolower($rawText));
            if ($regexResult) {
                return $regexResult;
            }
            $message = $res['message'] ?? 'Maaf, saya tidak dapat memahami permintaan tersebut.';
            return response()->json(['success' => false, 'is_html' => true, 'message' => $message]);
        }

        // Intent: New Item
        if ($intent === 'new_item') {
            $lokasiId = $res['lokasi_id'] ?? 1;
            $kategoriId = $res['kategori_id'] ?? 1;
            $loc = Lokasi::find($lokasiId);
            $kat = Kategori::find($kategoriId);

            return response()->json([
                'success' => true,
                'data' => [
                    'action' => 'new_item',
                    'nama_barang' => $res['nama_barang'] ?? $rawText,
                    'lokasi_id' => $lokasiId,
                    'kategori_id' => $kategoriId,
                    'lokasi_nama' => $loc ? $loc->nama : 'Gudang Utama',
                    'kategori_nama' => $kat ? $kat->nama : 'Umum',
                ]
            ]);
        }

        // Intent: New Kategori
        if ($intent === 'new_kategori' && !empty($res['nama'])) {
            return response()->json([
                'success' => true,
                'data' => [
                    'action' => 'new_kategori',
                    'nama' => $res['nama'],
                    'deskripsi' => $res['deskripsi'] ?? 'Dibuat via AI'
                ]
            ]);
        }

        // Intent: New Lokasi
        if ($intent === 'new_lokasi' && !empty($res['nama'])) {
            return response()->json([
                'success' => true,
                'data' => [
                    'action' => 'new_lokasi',
                    'nama' => $res['nama'],
                    'deskripsi' => $res['deskripsi'] ?? 'Dibuat via AI'
                ]
            ]);
        }

        // Intent: New Satuan
        if ($intent === 'new_satuan' && !empty($res['nama'])) {
            return response()->json([
                'success' => true,
                'data' => [
                    'action' => 'new_satuan',
                    'nama' => $res['nama'],
                    'keterangan' => $res['keterangan'] ?? 'Dibuat via AI'
                ]
            ]);
        }

        // Intent: Masuk, Keluar, Info
        if (in_array($intent, ['masuk', 'keluar', 'info'])) {
            $barang = null;
            if (!empty($res['barang_id'])) {
                $barang = Barang::with('satuan')->find($res['barang_id']);
            }

            if (!$barang && !empty($res['nama_barang'])) {
                $barang = Barang::with('satuan')->where('nama', 'like', '%' . $res['nama_barang'] . '%')->first();
            }

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => "Barang tidak ditemukan di database."
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'action' => $intent,
                    'qty' => (int)($res['qty'] ?? 1),
                    'barang_id' => $barang->id,
                    'nama_barang' => $barang->nama,
                    'stok' => $barang->stok,
                    'satuan' => $barang->satuan ? $barang->satuan->nama : 'unit',
                    'keterangan' => $res['keterangan'] ?? 'Dicatat via Gemini AI',
                    'dipakai_oleh' => $res['dipakai_oleh'] ?? '-',
                    'tujuan' => $res['tujuan'] ?? '-'
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Intent tidak dikenali.']);
    }

    /**
     * Fallback Engine: Regex Local Parser
     */
    protected function parseWithRegex(string $rawText, string $lowerText)
    {
        // 1. UNDO / BATAL
        if (preg_match('/(?:batal|undo|hapus).*(?:transaksi|barusan|tadi|terakhir)/i', $lowerText)) {
            return $this->executeUndo();
        }

        // 2. LAPORAN HARIAN
        if (preg_match('/(?:rekap|laporan).*(?:hari ini|harian)/i', $lowerText)) {
            return $this->executeRekap();
        }

        // 3. STOK KRITIS
        if (preg_match('/(?:stok|barang).*(?:kritis|krisis|habis|menipis)/i', $lowerText)) {
            return $this->executeStokKritis();
        }

        // 4. RIWAYAT
        if (preg_match('/(?:riwayat\s+([\w\s]+)|([\w\s]+?)(?:\s+hari ini|\s+bulan ini|\s+minggu ini|\s+kemarin)?\s+(?:ambil|pakai|ngambil|mengambil).*(?:apa))/i', $lowerText, $m)) {
            $who = !empty($m[1]) ? trim($m[1]) : (!empty($m[2]) ? trim($m[2]) : null);
            if ($who) {
                $history = BarangKeluar::with('barang')->where('dipakai_oleh', 'like', "%{$who}%")->orWhere('tujuan', 'like', "%{$who}%")->orderBy('created_at', 'desc')->limit(5)->get();
                if ($history->isEmpty()) {
                    $msg = "Tidak ditemukan riwayat pemakaian untuk <b>{$who}</b>.";
                } else {
                    $msg = "🔍 <b>Riwayat Pemakaian: {$who}</b><br><br>";
                    foreach ($history as $h) {
                        $date = $h->created_at->format('d/m/Y');
                        $msg .= "- {$date}: <b>{$h->jumlah} " . ($h->barang ? $h->barang->nama : 'Unknown') . "</b> (Tujuan: {$h->tujuan})<br>";
                    }
                }
                return response()->json(['success' => false, 'is_html' => true, 'message' => $msg]);
            }
        }

        // 5a. KATEGORI BARU
        if (preg_match('/(?:tambah|bikin|daftar|buat)\s+kategori\s*(?:baru\s+)?(.+)/i', $lowerText, $m)) {
            $nama = trim($m[1]);
            return response()->json([
                'success' => true,
                'data' => [
                    'action' => 'new_kategori',
                    'nama' => ucwords($nama),
                    'deskripsi' => 'Dibuat via Sistok AI'
                ]
            ]);
        }

        // 5b. LOKASI BARU
        if (preg_match('/(?:tambah|bikin|daftar|buat)\s+lokasi\s*(?:baru\s+)?(.+)/i', $lowerText, $m)) {
            $nama = trim($m[1]);
            return response()->json([
                'success' => true,
                'data' => [
                    'action' => 'new_lokasi',
                    'nama' => ucwords($nama),
                    'deskripsi' => 'Dibuat via Sistok AI'
                ]
            ]);
        }

        // 5c. SATUAN BARU
        if (preg_match('/(?:tambah|bikin|daftar|buat)\s+satuan\s*(?:baru\s+)?(.+)/i', $lowerText, $m)) {
            $nama = trim($m[1]);
            return response()->json([
                'success' => true,
                'data' => [
                    'action' => 'new_satuan',
                    'nama' => ucwords($nama),
                    'keterangan' => 'Dibuat via Sistok AI'
                ]
            ]);
        }

        // 5. BARANG BARU
        if (preg_match('/(?:tambah|bikin|daftar).*(?:barang baru)\s*(?:namanya\s+)?(.+)/i', $lowerText, $m)) {
            $remaining = trim($m[1]);
            $lokasiName = null;
            $kategoriName = null;

            if (preg_match('/lokasi\s+([a-z0-9\s]+?)(?=\s+kategori|$)/i', $remaining, $l)) {
                $lokasiName = trim($l[1]);
            }
            if (preg_match('/kategori\s+([a-z0-9\s]+?)(?=\s+lokasi|$)/i', $remaining, $k)) {
                $kategoriName = trim($k[1]);
            }

            $namaBarang = $remaining;
            if (preg_match('/^(.+?)(?=\s+(?:lokasi|kategori))/i', $remaining, $n)) {
                $namaBarang = trim($n[1]);
            }

            $lokasiId = 1;
            $kategoriId = 12;

            if ($lokasiName) {
                $loc = Lokasi::where('nama', 'like', "%{$lokasiName}%")->first();
                if ($loc) $lokasiId = $loc->id;
            }
            if ($kategoriName) {
                $kat = Kategori::where('nama', 'like', "%{$kategoriName}%")->first();
                if ($kat) $kategoriId = $kat->id;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'action' => 'new_item',
                    'nama_barang' => $namaBarang,
                    'lokasi_id' => $lokasiId,
                    'kategori_id' => $kategoriId,
                    'lokasi_nama' => $lokasiName ? (isset($loc) ? $loc->nama : $lokasiName) : 'Default',
                    'kategori_nama' => $kategoriName ? (isset($kat) ? $kat->nama : $kategoriName) : 'Default',
                ]
            ]);
        }

        $keterangan = 'Dicatat otomatis oleh Sistok AI';
        $dipakaiOleh = '-';
        $tujuan = '-';

        if (preg_match('/(?:dipakai|dipake|diambil)(?:\s+oleh)?\s+([a-zA-Z0-9\s]+?)(?=\s+(?:ket|keterangan|buat|untuk|tujuan|,|-)|$)/i', $rawText, $m)) {
            $dipakaiOleh = trim($m[1]);
        }

        if (preg_match('/(?:buat|untuk|tujuan(?:nya)?)\s+([a-zA-Z0-9\s]+?)(?=\s+(?:ket|keterangan|dipakai|dipake|diambil|,|-)|$)/i', $rawText, $m)) {
            $tujuan = trim($m[1]);
        }

        $parts = preg_split('/(?:,\s+|;\s+|-\s+)/', $rawText, 2);
        if (count($parts) > 1) {
            $text = trim(strtolower($parts[0]));
            $keterangan = trim($parts[1]) . ' (AI)';
        } else {
            $ketKeywords = [' ket ', ' keterangan ', ' dari ', ' untuk '];
            $text = strtolower($rawText);
            foreach ($ketKeywords as $kw) {
                if (strpos($text, $kw) !== false) {
                    $split = explode($kw, $rawText, 2);
                    $text = strtolower(trim($split[0]));
                    $keterangan = trim($kw) . ' ' . trim($split[1]) . ' (AI)';
                    break;
                }
            }
        }

        $action = null;
        $qty = null;

        $keywords = [
            'masuk' => 'masuk', 'tambah' => 'masuk', 'in' => 'masuk',
            'keluar' => 'keluar', 'kurang' => 'keluar', 'out' => 'keluar',
            'cek' => 'info', 'cari' => 'info', 'berapa' => 'info', 'stok' => 'info', 'sisa' => 'info', 'info' => 'info'
        ];

        $words = explode(' ', $text);

        foreach ($words as $index => $word) {
            if (array_key_exists($word, $keywords)) {
                if (!$action) {
                    $action = $keywords[$word];
                }

                if (isset($words[$index + 1]) && is_numeric($words[$index + 1]) && !$qty) {
                    $qty = (int)$words[$index + 1];
                    unset($words[$index], $words[$index + 1]);
                } elseif (isset($words[$index - 1]) && is_numeric($words[$index - 1]) && !$qty) {
                    $qty = (int)$words[$index - 1];
                    unset($words[$index], $words[$index - 1]);
                } else {
                    unset($words[$index]);
                }
            }
        }

        if (!$qty) {
            $reversed = array_reverse($words, true);
            foreach ($reversed as $index => $word) {
                if (is_numeric($word)) {
                    $qty = (int)$word;
                    unset($words[$index]);
                    break;
                }
            }
        }

        if (!$action) {
            return null;
        }

        if ($action !== 'info' && (!$qty || $qty <= 0)) {
            return response()->json(['success' => false, 'message' => 'Tidak mendeteksi jumlah barang (angka).']);
        }

        $query = trim(implode(' ', $words));
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Nama barang tidak ditemukan.']);
        }

        $barangs = Barang::with('satuan')->get();
        $bestMatch = null;
        $highestScore = 0;

        $queryWords = explode(' ', strtolower($query));

        foreach ($barangs as $b) {
            $dbName = strtolower($b->nama);
            similar_text($query, $dbName, $percent);

            foreach ($queryWords as $w) {
                if (strlen($w) > 1 && strpos($dbName, $w) !== false) {
                    $percent += 20;
                }
            }

            if ($percent > $highestScore) {
                $highestScore = $percent;
                $bestMatch = $b;
            }
        }

        if (!$bestMatch || $highestScore < 30) {
            return response()->json([
                'success' => false,
                'message' => "Barang dengan nama mirip '{$query}' tidak ditemukan."
            ]);
        }

        $barang = $bestMatch;

        return response()->json([
            'success' => true,
            'data' => [
                'action' => $action,
                'qty' => $qty,
                'barang_id' => $barang->id,
                'nama_barang' => $barang->nama,
                'stok' => $barang->stok,
                'satuan' => $barang->satuan ? $barang->satuan->nama : 'unit',
                'keterangan' => $keterangan,
                'dipakai_oleh' => $dipakaiOleh,
                'tujuan' => $tujuan
            ]
        ]);
    }

    protected function executeUndo()
    {
        $lastKeluar = BarangKeluar::where('user_id', auth()->id())->where('created_at', '>=', now()->subHour())->orderBy('created_at', 'desc')->first();
        $lastMasuk = BarangMasuk::where('user_id', auth()->id())->where('created_at', '>=', now()->subHour())->orderBy('created_at', 'desc')->first();

        $toUndo = null;
        $type = '';

        if ($lastKeluar && $lastMasuk) {
            if ($lastKeluar->created_at > $lastMasuk->created_at) {
                $toUndo = $lastKeluar;
                $type = 'keluar';
            } else {
                $toUndo = $lastMasuk;
                $type = 'masuk';
            }
        } elseif ($lastKeluar) {
            $toUndo = $lastKeluar;
            $type = 'keluar';
        } elseif ($lastMasuk) {
            $toUndo = $lastMasuk;
            $type = 'masuk';
        }

        if (!$toUndo) {
            return response()->json(['success' => false, 'is_html' => true, 'message' => 'Tidak ada transaksi yang bisa dibatalkan dalam 1 jam terakhir.']);
        }

        DB::transaction(function () use ($toUndo, $type) {
            $barang = Barang::find($toUndo->barang_id);
            if ($barang) {
                if ($type === 'keluar') {
                    $barang->increment('stok', $toUndo->jumlah);
                } else {
                    $barang->decrement('stok', $toUndo->jumlah);
                }
            }
            $toUndo->delete();
        });

        $jenisText = $type === 'keluar' ? 'Keluar' : 'Masuk';
        return response()->json([
            'success' => false,
            'is_html' => true,
            'message' => "✅ Transaksi {$jenisText} <b>{$toUndo->jumlah} " . ($toUndo->barang ? $toUndo->barang->nama : 'Barang') . "</b> berhasil dibatalkan dan stok telah dikembalikan."
        ]);
    }

    protected function executeRekap()
    {
        $keluar = BarangKeluar::with('barang')->whereDate('created_at', today())->get();
        $masuk = BarangMasuk::with('barang')->whereDate('created_at', today())->get();

        $msg = "📊 <b>Rekap Transaksi Hari Ini</b><br><br>";
        $msg .= "<b>Barang Keluar:</b> " . $keluar->count() . " transaksi<br>";
        foreach ($keluar as $k) {
            $msg .= "- {$k->jumlah} " . ($k->barang ? $k->barang->nama : 'Unknown') . " (Oleh: {$k->dipakai_oleh})<br>";
        }
        $msg .= "<br><b>Barang Masuk:</b> " . $masuk->count() . " transaksi<br>";
        foreach ($masuk as $m) {
            $msg .= "- {$m->jumlah} " . ($m->barang ? $m->barang->nama : 'Unknown') . "<br>";
        }

        return response()->json(['success' => false, 'is_html' => true, 'message' => $msg]);
    }

    protected function executeStokKritis()
    {
        $barangs = Barang::with(['satuan', 'lokasi'])->where('stok', '<=', 5)->orderBy('stok', 'asc')->get();
        if ($barangs->isEmpty()) {
            $msg = "✅ Gudang aman! Tidak ada barang dengan stok kritis (di bawah 5).";
        } else {
            $msg = "🚨 <b>Peringatan Stok Kritis!</b><br><br>";
            foreach ($barangs as $b) {
                $satuan = $b->satuan ? $b->satuan->nama : 'unit';
                $lokasi = $b->lokasi ? $b->lokasi->nama : '-';
                $msg .= "- <b>{$b->nama}</b>: sisa <span style='color:red;'>{$b->stok} {$satuan}</span> di {$lokasi}<br>";
            }
        }
        return response()->json(['success' => false, 'is_html' => true, 'message' => $msg]);
    }

    public function confirmTransaction(Request $request)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['success' => false, 'message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        // Fitur Barang Baru
        if ($request->action === 'new_item') {
            $request->validate([
                'nama_barang' => 'required|string|max:255',
                'lokasi_id' => 'required|integer',
                'kategori_id' => 'required|integer'
            ]);

            $barang = Barang::create([
                'nama' => $request->nama_barang,
                'kategori_id' => $request->kategori_id,
                'satuan_id' => 12, // Asumsi 12 adalah Pcs / default
                'lokasi_id' => $request->lokasi_id,
                'stok' => 0,
                'min_stok' => 0,
                'user_id' => auth()->id()
            ]);

            return response()->json(['success' => true, 'message' => "Barang baru <b>{$barang->nama}</b> (Kode: {$barang->kode}) berhasil didaftarkan!"]);
        }

        // Fitur Kategori Baru
        if ($request->action === 'new_kategori') {
            $request->validate(['nama' => 'required|string|max:255']);
            $kategori = Kategori::firstOrCreate(
                ['nama' => $request->nama],
                ['deskripsi' => $request->deskripsi ?? 'Dibuat via Sistok AI']
            );
            return response()->json(['success' => true, 'message' => "Kategori baru <b>{$kategori->nama}</b> berhasil ditambahkan!"]);
        }

        // Fitur Lokasi Baru
        if ($request->action === 'new_lokasi') {
            $request->validate(['nama' => 'required|string|max:255']);
            $lokasi = Lokasi::firstOrCreate(
                ['nama' => $request->nama],
                ['deskripsi' => $request->deskripsi ?? 'Dibuat via Sistok AI']
            );
            return response()->json(['success' => true, 'message' => "Lokasi baru <b>{$lokasi->nama}</b> berhasil ditambahkan!"]);
        }

        // Fitur Satuan Baru
        if ($request->action === 'new_satuan') {
            $request->validate(['nama' => 'required|string|max:255']);
            $satuan = Satuan::firstOrCreate(
                ['nama' => $request->nama],
                ['keterangan' => $request->keterangan ?? 'Dibuat via Sistok AI']
            );
            return response()->json(['success' => true, 'message' => "Satuan baru <b>{$satuan->nama}</b> berhasil ditambahkan!"]);
        }

        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'action' => 'required|in:masuk,keluar',
            'qty' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
            'dipakai_oleh' => 'nullable|string',
            'tujuan' => 'nullable|string'
        ]);

        $keterangan = $request->input('keterangan', 'Dicatat otomatis oleh Sistok AI');
        $dipakaiOleh = $request->input('dipakai_oleh', '-');
        $tujuan = $request->input('tujuan', '-');

        $barang = Barang::findOrFail($request->barang_id);
        $lokasi = Lokasi::firstOrCreate(['nama' => 'Gudang Utama']);

        DB::transaction(function () use ($request, $barang, $lokasi, $keterangan, $dipakaiOleh, $tujuan) {
            if ($request->action === 'masuk') {
                BarangMasuk::create([
                    'tanggal' => date('Y-m-d'),
                    'barang_id' => $barang->id,
                    'lokasi_id' => $lokasi->id,
                    'jumlah' => $request->qty,
                    'keterangan' => $keterangan,
                    'user_id' => auth()->id()
                ]);
                $barang->increment('stok', $request->qty);
            } else {
                if ($barang->stok < $request->qty) {
                    throw new \Exception("Stok tidak mencukupi. Sisa stok: " . $barang->stok);
                }

                BarangKeluar::create([
                    'tanggal' => date('Y-m-d'),
                    'barang_id' => $barang->id,
                    'lokasi_id' => $lokasi->id,
                    'jumlah' => $request->qty,
                    'dipakai_oleh' => $dipakaiOleh,
                    'tujuan' => $tujuan,
                    'keterangan' => $keterangan,
                    'user_id' => auth()->id()
                ]);
                $barang->decrement('stok', $request->qty);
            }
        });

        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dicatat oleh AI.']);
    }
}
