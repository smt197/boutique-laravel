<?php

namespace App\Models;

use App\Observers\DetteObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['montantTotal', 'montantRestant', 'client_id', 'date_echeance'];

    protected $hidden = ['updated_at', 'created_at','client_id'];

    protected $appends = ['montantVerse'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')
                    ->withPivot('qteVente', 'prixVente');
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    /**
     * Accesseur pour l'attribut `montantVerse`, qui n'est pas stocké en base de données.
     */
    public function getMontantVerseAttribute()
    {
        // Retourner la somme des paiements comme montantVersé
        return $this->paiements()->sum('montant');
    }

    public function getMontantRestantAttribute()
    {
        return $this->montantTotal - $this->montantVerse;
    }

    protected static function boot()
    {
        parent::boot();
        static::observe(DetteObserver::class);
    }
}
