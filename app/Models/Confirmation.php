<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    use HasFactory;
    protected $fillable=['nim', 'bukti_pembayaran'];
    
    public function user(){
        return $this->belongsTo(User::class, 'nim', 'id');
    }
}
