<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = ['montant', 'dette_id','id'];

    public function dette(){
        return $this->belongsTo(Dette::class);
    }

    
}
