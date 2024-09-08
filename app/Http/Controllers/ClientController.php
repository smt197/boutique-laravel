<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientCompteRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use App\Services\UploadService;
use App\Services\QRCodeService;
use App\Services\EmailService;
use App\Services\PdfService;
use App\Jobs\SendClientEmailJob; 
use App\Traits\RestResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Enums\StatusResponseEnum;
use App\Events\PhotoUploaded;
use App\Models\Role;
use App\Models\User;
use App\Exceptions\ControllerError;

class ClientController extends Controller
{
    use RestResponseTrait;

    protected $clientService;
    protected $uploadService;
    protected $qrCodeService;
    protected $emailService;
    protected $pdfService;


    public function __construct(ClientService $clientService, UploadService $uploadService, QRCodeService $qrCodeService, PdfService $pdfService)
    {
        $this->clientService = $clientService;
        $this->uploadService = $uploadService;
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
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
            $client = $this->clientService->storeClient($request->validated());
            return new ClientResource($client);
        } catch (ControllerError $e) {
            return ['error' => $e->getMessage(), StatusResponseEnum::ECHEC, 'Erreur lors de la création du client', 500];
        } catch (\Throwable $e) {
            return ['error' => 'Erreur inattendue: ' . $e->getMessage(), StatusResponseEnum::ECHEC, 'Erreur lors de la création du client', 500];
        }
    }
    public function show(string $id)
    {
        try {
            $client = $this->clientService->getClientById($id);
            $this->authorize('view', $client);

            return new ClientResource($client);
        } catch (\Throwable $e) {
            return $this->sendResponse(['error' => 'Erreur lors de la récupération du client: ' . $e->getMessage()], StatusResponseEnum::ECHEC, 'Erreur lors de la récupération du client', 500);
        }
    }


    public function showClientByTelephone(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        try {
            $validated = $request->validate(['telephone' => 'required|string|size:9']);
            $client = $this->clientService->getClientWithPhotoInBase64($validated['telephone']);

            if ($client) {
                return new ClientResource($client);
            } else {
                return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
            }
        } catch (\Throwable $e) {
            return $this->sendResponse(['error' => 'Erreur lors de la recherche du client par téléphone: ' . $e->getMessage()], StatusResponseEnum::ECHEC, 'Erreur lors de la recherche du client', 500);
        }
    }

    public function addUserToClient(UpdateClientCompteRequest $request, $id)
    {
        $this->authorize('create', Client::class);

        try {
            $client = $this->clientService->addUserToClient($id, $request->validated());
            return new ClientResource($client);
        } catch (\Throwable $e) {
            return $this->sendResponse(['error' => 'Erreur lors de l\'ajout du compte utilisateur au client: ' . $e->getMessage()], StatusResponseEnum::ECHEC, 'Erreur lors de l\'ajout du compte utilisateur', 500);
        }
    }

    public function listDettesClient($id)
    {
        try {
            $client = $this->clientService->getClientWithDettes($id);
            $this->authorize('view', $client);

            if (!$client) {
                return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
            }

            return $this->sendResponse($client->dettes, StatusResponseEnum::SUCCESS, 'Dettes récupérées avec succès', 200);
        } catch (\Throwable $e) {
            return $this->sendResponse(['error' => 'Erreur lors de la récupération des dettes du client: ' . $e->getMessage()], StatusResponseEnum::ECHEC, 'Erreur lors de la récupération des dettes', 500);
        }
    }

    public function showClientWithUser($id)
    {
        try {
            $client = $this->clientService->getClientWithUser($id);
            $this->authorize('view', $client);

            if (!$client) {
                return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
            }

            return new ClientResource($client);
        } catch (\Throwable $e) {
            return $this->sendResponse(['error' => 'Erreur lors de la récupération des informations du client: ' . $e->getMessage()], StatusResponseEnum::ECHEC, 'Erreur lors de la récupération des informations du client', 500);
        }
    }
}
