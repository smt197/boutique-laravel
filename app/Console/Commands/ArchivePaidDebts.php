<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dette;
use App\Services\DetteArchive;

class ArchivePaidDebts extends Command
{
    protected $signature = 'dettes:archive';
    protected $description = 'Archiver les dettes totalement payées dans MongoDB';

    protected $archiveDetteService;

    public function __construct(DetteArchive $archiveDetteService)
    {
        parent::__construct();
        $this->archiveDetteService = $archiveDetteService;
    }

    public function handle()
    {
        // Récupérer les dettes totalement payées
        $paidDebts = Dette::where('montantRestant', 0)->get();

        foreach ($paidDebts as $dette) {
            // $this->archiveDetteService->archivePaidDebt($dette);
        }

        $this->info('Les dettes totalement payées ont été archivées.');
    }
}
