<?php
namespace App\Services;

use App\Repositories\DetteRepository;
use App\Repositories\PaiementRepository;
use Illuminate\Support\Facades\DB;

class DetteServiceImpl implements DetteService
{
    protected $detteRepository;
    protected $paiementRepository;

    // Injection des repositories via le constructeur
    public function __construct(DetteRepository $detteRepository, PaiementRepository $paiementRepository)
    {
        $this->detteRepository = $detteRepository;
        $this->paiementRepository = $paiementRepository;
    }

    public function getAll(array $filters, array $includes)
    {
        return $this->detteRepository->getAll($filters, $includes);
    }

    public function show($id){
        // Récupérer la dette via le repository
        $dette = $this->detteRepository->findById($id);

        if (!$dette) {
            return response()->json(['error' => 'Dette introuvable'], 404);
        }
        return $dette;
    }

    public function getDetteWithArticles($id){

        $dette = $this->detteRepository->findById($id);

        if (!$dette) {
            return response()->json(['error' => 'Dette introuvable'], 404);
        }

        // Charger les articles associés à la dette
        $dette->load('articles');

        return $dette;
    }
    public function getDetteWithPaiements($id){
        $dette = $this->detteRepository->findById($id);

        if (!$dette) {
            return response()->json(['error' => 'Dette introuvable'], 404);
        }

        // Charger les paiements associés à la dette
        $dette->load('paiements');

        return $dette;
    }

    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            // Créer une nouvelle dette via le repository
            $detteData = [
                'montantTotal' => $data['montant'],
                'client_id' => $data['clientId'],
                'montantRestant' => $data['montant'], // Initialement, montantRestant = montantTotal
            ];

            $dette = $this->detteRepository->create($detteData);

            DB::commit();

            return $dette;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
