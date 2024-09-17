<?php

namespace App\Models;

use App\Observers\ClientObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ClientObserver::class])]
class Client extends Model
{
    use HasFactory;

    // public mixed $user_id;
    protected $fillable = [
        'id',
        'surname',
        'adresse',
        'telephone',
        'user_id',
        'qrcode',
        'categorie_id',
        'montant_max'
    ];
    protected $hidden = [
        //  'password',
        'created_at',
        'updated_at',
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    public function scopeCompte($query, $value)
    {
        if ($value == 'oui') {
            return $query->whereNotNull('user_id');
        } elseif ($value == 'non') {
            return $query->whereNull('user_id');
        }
        return $query;
    }

    public function scopeEtat($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            $etat = $value == 'oui' ? true : false;
            $query->where('etat', $etat);
        });
    }

    

    function scopeWithUser($query){
        return $query->with('user');
    }
}
