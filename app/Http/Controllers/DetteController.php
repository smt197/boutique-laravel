<?php
namespace App\Http\Controllers;

use App\Services\DetteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreDetteRequest;
use App\Models\Dette;

class DetteController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    public function store(StoreDetteRequest $request)
    {
        $this->authorize('create', Dette::class);

        try {
            $dette = $this->detteService->store($request->validated());

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
