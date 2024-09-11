<?php

namespace App\Models;

use App\Observers\DetteObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([DetteObserver::class])]
class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['montant', 'client_id'];

    protected $appends = ["montant_verse", "montant_restant"];

    public function getMontantVerseAttribute(){
        return $this->paiements()->sum('montant');
    }

    public function getMontantRestantAttribute(){
        return $this->montant - $this->montant_verse;
    }

    public function articles(){
        return $this->belongsToMany(Article::class)
            ->withPivot('quantite','prixVente')
            ->withTimestamps();
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function paiements(){
        return $this->hasMany(Paiement::class);
    }

    // Scope for solded debts (fully paid)
    public function scopeSoldes($query)
    {
        return $query->whereHas('paiements', function ($q) {
            $q->select('dette_id')
                ->groupBy('dette_id')
                ->havingRaw('SUM(montant) >= dettes.montant');
        });
    }

    public function scopeNonSoldes($query, $flag = true)
{
    if ($flag) {
        return $query->where(function ($q) {
            $q->doesntHave('paiements')
              ->orWhereHas('paiements', function ($q) {
                  $q->select('dette_id')
                    ->groupBy('dette_id')
                    ->havingRaw('SUM(montant) < dettes.montant');
              });
        });
    }
}

}
