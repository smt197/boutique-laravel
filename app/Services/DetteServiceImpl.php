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
