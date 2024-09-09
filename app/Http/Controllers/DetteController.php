<?php
namespace App\Http\Controllers;

use App\Services\DetteService;
use App\Repositories\DetteRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreDetteRequest;
use App\Http\Resources\DetteResource;
use App\Http\Resources\DetteResourceClient;
use App\Models\Dette;
use Illuminate\Http\JsonResponse;

class DetteController extends Controller
{
    protected $detteService;
    protected $detteRepository;

    public function __construct(DetteService $detteService, DetteRepository $detteRepository)
    {
        $this->detteService = $detteService;
        $this->detteRepository = $detteRepository;
    }

    public function index(Request $request){
        $this->authorize('viewAny', Dette::class);
        $filters = $request->only(['statut']);
        $includes = $request->input('include', []);

        try {
            $dettes = $this->detteService->getAll($filters, $includes);

            return $dettes;

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des dettes',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id){
        $this->authorize('viewAny', Dette::class);

        try {
            $dette = $this->detteService->show($id);

            $dette->load('client');

            return response()->json([
                'data' => $dette,
                'message' => 'Dette trouvee avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
               'message' => 'Dette non trouvee',
            ], 500);
        }
    }

    public function getDetteWithArticles($id){
        $this->authorize('viewAny', Dette::class);

        try {
            $dette = $this->detteService->getDetteWithArticles($id);

            return response()->json([
                'data' => $dette,
                'message' => 'Dette with articles retrieved'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
               'message' => 'Dette non trouvée',
            ], 500);
        }
    }
    public function getDetteWithPaiements($id){
        $this->authorize('viewAny', Dette::class);

        try {
            $dette = $this->detteService->getDetteWithPaiements($id);

            return response()->json([
                'data' => $dette,
                'message' => 'Dette with Paiements retrieved'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
               'message' => 'Dette non trouvée',
            ], 500);
        }
    }


    public function store(StoreDetteRequest $request)
    {
        $this->authorize('create', Dette::class);

        try {
            $dette = $this->detteService->store($request->validated());

            return response()->json([
                'data' => new DetteResource($dette), // Utilisation de la resource DetteResource
                'message' => 'Dette enregistrée avec succès'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement de la dette',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function StorePaiement(int $detteId, Request $request): JsonResponse
    {
        // Valide que le montant est présent et qu'il est numérique et positif
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0.01',
        ]);

        // Appelle le repository avec le montant
        $dette = $this->detteRepository->addPaiement($detteId, $validatedData['montant']);
        $dette->load('paiements');

        return response()->json([
            'data' => $dette,
            'message' => 'Paiement ajouté avec succès',
            'status' => 200,
        ], 200);
    }

}
