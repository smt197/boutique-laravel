<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'reference', 'prix', 'quantite'];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Scope a query to filter articles by libelle.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $libelle
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function WhereLibelle($libelle)
    {
        return self::where('libelle', $libelle);
    }
}
