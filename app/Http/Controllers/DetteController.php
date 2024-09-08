<?php
namespace App\Http\Controllers;

use App\Services\DetteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DetteController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement.montant' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Erreur de validation'
            ], 411);
        }

        $data = $request->all();

        try {
            $dette = $this->detteService->store($data);

            return response()->json([
                'data' => $dette,
                'message' => 'Dette enregistrée avec succès'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement de la dette',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
