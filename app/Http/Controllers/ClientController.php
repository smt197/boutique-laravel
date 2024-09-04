<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientCompteRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Client;
use App\Enums\StatusResponseEnum;

class ClientController extends Controller
{
    use RestResponseTrait;

    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
        $this->authorizeResource(Client::class, 'client');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $clients = $this->clientService->getAllClients($request);
        return new ClientCollection($clients);
    }

    public function store(StoreClientRequest $request)
    {
        try {
            $client = $this->clientService->createClient($request->validated());
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client créé avec succès', 201);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }

    public function show(string $id)
    {
        $client = $this->clientService->getClientById($id);
        $this->authorize('view', $client);

        return new ClientResource($client);
    }

    public function showClientByTelephone(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $validated = $request->validate(['telephone' => 'required|string|size:9']);
        // $client = $this->clientService->getClientByTelephone($validated['telephone']);
        $client = $this->clientService->getClientWithPhotoInBase64($validated['telephone']);

        if ($client) {
            return new ClientResource($client);
        } else {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }
    }

    public function addUserToClient(UpdateClientCompteRequest $request, $id)
    {
        $this->authorize('create', Client::class);

        try {
            $client = $this->clientService->addUserToClient($id, $request->validated());
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Compte utilisateur ajouté avec succès au client.');
        } catch (Exception $e) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Une erreur est survenue lors de l\'ajout du compte utilisateur au client.', 500);
        }
    }

    public function listDettesClient($id)
    {
        $client = $this->clientService->getClientWithDettes($id);
        $this->authorize('view', $client);

        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }

        return $this->sendResponse($client->dettes, StatusResponseEnum::SUCCESS, 'Liste des dettes récupérée avec succès');
    }

    public function showClientWithUser($id)
    {
        $client = $this->clientService->getClientWithUser($id);
        $this->authorize('view', $client);

        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }

        return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Informations du client récupérées avec succès');
    }
}
