<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['libelle', 'prix', 'quantite', 'user_id'];
    protected $hidden = ['created_at', 'updated_at','id','deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->belongsToMany(Dette::class)
            ->withPivot('quantite', 'prixVente')
            ->withTimestamps();
    }

    public function scopeDisponible($query, $disponible)
    {
        if ($disponible === 'false') {
            return $query->where('quantite', '=', 0);
        }

        if ($disponible === 'true') {
            return $query->where('quantite', '>=', 1);
        }

        return $query->where('quantite', '>', -1);
    }

    public function scopeFilterBySurname($query, $surname)
    {
        if ($surname) {
            return $query->where('surname', 'like', "%{$surname}%");
        }

        return $query;
    }

    public function scopeFilterByUsername($query, $username)
    {
        if ($username) {
            return $query->whereHas('user', function ($q) use ($username) {
                $q->where('username', 'like', "%{$username}%");
            });
        }
    }
}
