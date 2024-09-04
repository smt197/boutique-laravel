<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'reference', 'prix', 'quantite'];

    protected $hidden = ['created_at', 'updated_at'];

    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dette')->withPivot('qteVente', 'prixVente');
    }

    /**
     * Scope a query to filter clients by telephone.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $telephone
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function WhereLibelle($libelle)
    {
        return self::where('libelle', $libelle);
    }
}
