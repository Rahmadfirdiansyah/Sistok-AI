<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama', 'deskripsi'])]
class Kategori extends Model
{
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
