<?php

namespace App\Http\Controllers;

use App\Models\ModelFirebase;
use App\Services\Dette\ArchiveService;

class RecupArchiveController extends Controller
{
    protected $archiveService;

    public function __construct(ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    public function getArchivedDebtsByClient($id)
    {
        $this->authorize('viewAnys', ModelFirebase::class);

        $archivedDebts = $this->archiveService->getArchivedDebtsByClient($id);
        return response()->json($archivedDebts);
    }
    public function getAllArchivedDebts()
    {
        // $this->authorize('viewAnys', ModelFirebase::class);

        $allArchivedDebts = $this->archiveService->getAllArchivedDebts();
        return response()->json($allArchivedDebts);
    }
    public function getArchivedDebtById($Id){
        $this->authorize('viewAnys', ModelFirebase::class);

        $archivedDebt = $this->archiveService->getArchivedDebtById($Id);
        return response()->json($archivedDebt);
    }

    public function restore($debtId)
    {
        $result = $this->archiveService->restoreArchivedDebt($debtId);
        
        return response()->json($result, $result['code']);
    }












}