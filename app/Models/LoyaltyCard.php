<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
    use HasFactory;

    // Spécifiez la table si elle n'est pas au pluriel du modèle
    protected $table = 'loyalty_cards';

    // Les attributs qui sont assignables en masse
    protected $fillable = [
        'surname',
        'telephone',
        'photo',
        'qr_code',
    ];

    // Les attributs que vous souhaitez cacher dans les tableaux
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
