<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'montant', 'articles','status'];

    protected $hidden = ['updated_at', 'created_at'];

    protected $casts = [
        'articles' => 'array', // Assurez-vous que les articles sont castés en tant que tableau
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    protected static function boot()
    {
        parent::boot();
        // Ajoutez des comportements supplémentaires si nécessaire
    }
}
