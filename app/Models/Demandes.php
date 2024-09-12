<?php

namespace App\Models;

use App\Observers\CreateDemande;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([CreateDemande::class])]
class Demandes extends Model
{
    use HasFactory;

    protected $fillable = ['etat','client_id','montant'];

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function articles(){
        return $this->belongsToMany(Article::class)->withPivot('quantite');
    }

    public function scopeEnCours($query){
        return $query->where('etat','En attente');
    }

    public function scopeTraite($query){
        return $query->where('etat','Traite');
    }

    public function scopeAnnule($query){
        return $query->where('etat','Annule');
    }


}
