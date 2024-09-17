<?php

namespace App\Models;

use App\Scopes\TelephoneScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['surname','telephone', 'adresse','categorie_id','max_montant', 'user_id','qr_code'];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function dettes() {
        return $this->hasMany(Dette::class);
    }

    public function routeNotificationForSms()
    {
        return $this->telephone;  // Champ utilisé pour l'envoi des SMS
    }


    public function calculateTotalDebt()
    {
        return $this->dettes()->sum('montantRestant');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new TelephoneScope(request()->get('telephone')));
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    public function categorie()
    {
        return $this->belongsTo(CategoryClient::class, 'categorie_id');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at'); // Retourne le constructeur de requêtes
    }

    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at'); // Retourne le constructeur de requêtes
    }
}
