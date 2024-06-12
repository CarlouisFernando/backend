<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $tables = 'jadwals';
    
    public $timestamps = true;
    public $fillable = ['jam_awal','jam_akhir','tanggal','kelas'];
    
}
