<?php
namespace App\Repositories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ClientRepositoryImpl implements ClientRepository
{
    public function all()
    {
        return Client::all();
    }

    public function find($id)
    {
        return Client::find($id);
    }

    public function create(array $data)
    {
        return Client::create($data);
    }

    public function update($id, array $data)
    {
        $client = $this->find($id);
        $client->update($data);
        return $client;
    }

    public function delete($id)
    {
        $client = $this->find($id);
        $client->delete();
        return $client;
    }

    public function findByTelephone($telephone)
    {
        return Client::with('user:id,nom,prenom,login,photo')->firstOrFail();
    }

    public function addUserToClient($id, array $data)
    {
        $client = $this->find($id);

        if ($client->user_id) {
            throw new \Exception('Ce client a déjà un compte utilisateur.');
        }

        $user = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'login' => $data['login'],
            'password' => bcrypt($data['password']),
            'photo' => $data['photo'],
            'role_id' => $data['role_id'],
        ]);

        $client->user_id = $user->id;
        $client->save();

        return $client;
    }

    public function getClientsWithFilters(Request $request)
    {
        $query = Client::query();

        if ($request->has('comptes')) {
            if ($request->input('comptes') === 'oui') {
                $query->whereNotNull('user_id');
            } elseif ($request->input('comptes') === 'non') {
                $query->whereNull('user_id');
            }
        }

        if ($request->has('active')) {
            if ($request->input('active') === 'oui') {
                $query->whereHas('user', function ($q) {
                    $q->where('active', 'OUI');
                });
            } elseif ($request->input('active') === 'non') {
                $query->whereHas('user', function ($q) {
                    $q->where('active', 'NON');
                });
            }
        }

        $query->with('user:id,nom,prenom,login,photo,active');

        return $query->get();
    }

    public function findWithDettes($id)
    {
        return Client::with('dettes')->find($id);
    }

    public function findWithUser($id)
    {
        return Client::with('user:id,nom,prenom,login,photo')->find($id);
    }
}
