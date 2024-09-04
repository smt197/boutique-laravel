<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientCompteRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use App\Services\UploadService;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Enums\StatusResponseEnum;
use App\Models\Role;
use App\Models\User;
use App\Exceptions\ControllerError;

class ClientController extends Controller
{
    use RestResponseTrait;

    protected $clientService;
    protected $uploadService;

    public function __construct(ClientService $clientService, UploadService $uploadService)
    {
        $this->clientService = $clientService;
        $this->uploadService = $uploadService;
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
        DB::beginTransaction();

        try {
            // Extraire les données du client de la requête
            $clientData = $request->only(['surname', 'adresse', 'telephone']);
            
            // Créer le client
            $client = Client::create($clientData);
            
            // Vérifier si les données utilisateur sont fournies
            if ($request->has('user')) {
                $roleId = $request->input('user.role_id');
                $role = Role::find($roleId);
                
                if (!$role) {
                    throw new ControllerError("Le rôle spécifié n'existe pas.");
                }
                
                // Préparer les données utilisateur
                $userData = [
                    'nom' => $request->input('user.nom'),
                    'prenom' => $request->input('user.prenom'),
                    'login' => $request->input('user.login'),
                    'password' => bcrypt($request->input('user.password')), // Hash du mot de passe
                    'role_id' => $role->id
                ];

                // Gérer l'image si elle est fournie
                if ($request->hasFile('user.photo')) {
                    $image = $request->file('user.photo');
                    $imageContents = file_get_contents($image->getRealPath());
                    $userData['photo'] = base64_encode($imageContents);
                } else {
                    $userData['photo'] = null;
                }

                // Créer l'utilisateur associé
                $user = User::create($userData);
                
                // Associer l'utilisateur avec le client
                $client->user()->associate($user);
                $client->save();
            }

            DB::commit();
            return new ClientResource($client);

        } catch (ControllerError $e) {
            DB::rollBack();
            throw new ControllerError('Erreur lors de la création du client: ' . $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new ControllerError('Erreur inattendue: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $client = $this->clientService->getClientById($id);
            $this->authorize('view', $client);

            return new ClientResource($client);
        } catch (\Throwable $e) {
            throw new ControllerError('Erreur lors de la récupération du client: ' . $e->getMessage());
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
                return [null, 'Statut'=>'ECHEC', 'message'=>'Client non trouvé', 'code'=>'404'];
            }
        } catch (\Throwable $e) {
            throw new ControllerError('Erreur lors de la recherche du client par téléphone: ' . $e->getMessage());
        }
    }

    public function addUserToClient(UpdateClientCompteRequest $request, $id)
    {
        $this->authorize('create', Client::class);

        try {
            $client = $this->clientService->addUserToClient($id, $request->validated());
            return new ClientResource($client);
        } catch (\Throwable $e) {
            throw new ControllerError('Erreur lors de l\'ajout du compte utilisateur au client: ' . $e->getMessage());
        }
    }

    public function listDettesClient($id)
    {
        try {
            $client = $this->clientService->getClientWithDettes($id);
            $this->authorize('view', $client);

            if (!$client) {
                return [null, 'Statut'=>'ECHEC', 'message'=>'Client non trouvé', 'code'=>'404'];
            }

            return ($client->dettes);
        } catch (\Throwable $e) {
            throw new ControllerError('Erreur lors de la récupération des dettes du client: ' . $e->getMessage());
        }
    }

    public function showClientWithUser($id)
    {
        try {
            $client = $this->clientService->getClientWithUser($id);
            $this->authorize('view', $client);

            if (!$client) {
                return [null, 'Statut'=>'ECHEC', 'message'=>'Client non trouvé', 'code'=>'404'];
            }

            return new ClientResource($client);
        } catch (\Throwable $e) {
            throw new ControllerError('Erreur lors de la récupération des informations du client: ' . $e->getMessage());
        }
    }
}
