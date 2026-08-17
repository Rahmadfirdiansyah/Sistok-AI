<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama', 'keterangan'])]
class Satuan extends Model
{
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
