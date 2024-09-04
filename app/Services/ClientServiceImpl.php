<?php
namespace App\Services;

use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientServiceImpl implements ClientService
{
    protected $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getAllClients(Request $request)
    {
        return $this->clientRepository->getClientsWithFilters($request);
    }

    public function createClient(array $data)
    {
        return $this->clientRepository->create($data);
    }

    public function getClientById($id)
    {
            return $this->clientRepository->find($id);
    }

    public function getClientByTelephone($telephone)
    {
        return $this->clientRepository->findByTelephone($telephone);
    }

    public function addUserToClient($id, array $data)
    {
        return $this->clientRepository->addUserToClient($id, $data);
    }

    public function getClientWithDettes($id)
    {
        return $this->clientRepository->findWithDettes($id);
    }

    public function getClientWithUser($id)
    {
        return $this->clientRepository->findWithUser($id);
    }

    public function updateClient($id, array $data){
        return $this->clientRepository->update($id, $data);
    }
    public function deleteClient($id){
        return $this->clientRepository->delete($id);
    }



    public function getClientWithPhotoInBase64($telephone)
{
    // Récupérer le client en fonction du numéro de téléphone
    $client = $this->getClientByTelephone($telephone);

    // Vérifier si le client existe et s'il a une photo
    if ($client && $client->photo) {
        try {
            // Lire le contenu de l'image depuis l'URL
            $photoContent = file_get_contents($client->photo);
            
            // Vérifier si le contenu a été correctement récupéré
            if ($photoContent !== false) {
                // Convertir le contenu de l'image en base64
                $client->photo = base64_encode($photoContent);
            } else {
                // En cas d'échec de la récupération, mettre une valeur par défaut ou gérer l'erreur
                $client->photo = null;
            }
        } catch (\Exception $e) {
            // Gérer les exceptions en cas de problème avec la récupération ou la conversion
            $client->photo = null;
        }
    }

    // Retourner l'objet client avec la photo en base64 (si disponible)
    return $client;
}


}
