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
            'client' => new ClientResource($this->whenLoaded('client')),
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
            'created_at' => $this->created_at,
        ];
    }
}
