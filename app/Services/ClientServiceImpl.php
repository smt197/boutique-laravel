<?php
namespace App\Services;

use App\Repositories\ClientRepository;
use Illuminate\Http\Request;

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

}
