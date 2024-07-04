<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable=['nim', 'periode', 'semester', 'total_pembayaran'];
    
    public function user(){
        return $this->belongsTo(User::class, 'nim', 'id');
    }
}
