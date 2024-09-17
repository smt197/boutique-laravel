<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryClient extends Model
{
    use HasFactory;

    // Specify the table associated with the model.
    protected $table = 'categories_clients';

    // Specify which attributes are mass assignable.
    protected $fillable = [
        'libelle',
    ];
}
