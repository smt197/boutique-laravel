<?php
namespace App\Services;

use Illuminate\Http\Request;

interface DetteService
{
    public function getAll(array $filters, array $includes);
    public function store(array $data);
    public function show($id);
    public function getDetteWithArticles($id);
    public function getDetteWithPaiements($id);
}
