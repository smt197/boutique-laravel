<?php
namespace App\Services;

use Illuminate\Http\Request;

interface ClientService
{
    public function getAllClients(Request $request);
    public function getClientById($id);
    public function createClient(array $data);
    public function updateClient($id, array $data);
    public function deleteClient($id);
    public function getClientByTelephone($telephone);
    public function addUserToClient($id, array $data);
    public function getClientWithDettes($id);
    public function getClientWithUser($id);
}
