<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'montantTotal' => $this->montantTotal,
            'montantRestant' => $this->montantRestant,
            'date_echeance' => $this->date_echeance,
            'client' => new ClientResource($this->client),
            'articles' => ArticleResource::collection($this->articles),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
        ];
        
    }
}
