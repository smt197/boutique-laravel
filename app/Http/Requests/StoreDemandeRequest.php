<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandeRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette demande.
     *
     * @return bool
     */
    public function authorize()
    {
        // Vous pouvez ajouter une logique ici pour vérifier si l'utilisateur est autorisé à effectuer cette action.
        // Pour l'instant, on permet à tous les utilisateurs de faire cette demande.
        return true;
    }

    /**
     * Règles de validation pour la demande.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'montant' => 'required|numeric',
            'articles' => 'required|array',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|numeric|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
        ];
    }

    /**
     * Messages de validation personnalisés.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'articles.required' => 'Les articles sont requis.',
            'articles.array' => 'Les articles doivent être un tableau.',
            'articles.*.article_id.required' => 'L\'ID de l\'article est requis.',
            'articles.*.article_id.exists' => 'L\'article spécifié n\'existe pas.',
            'articles.*.qteVente.required' => 'La quantité de vente est requise.',
            'articles.*.qteVente.numeric' => 'La quantité de vente doit être un nombre.',
            'articles.*.qteVente.min' => 'La quantité de vente doit être au moins 1.',
            'articles.*.prixVente.required' => 'Le prix de vente est requis.',
            'articles.*.prixVente.numeric' => 'Le prix de vente doit être un nombre.',
            'articles.*.prixVente.min' => 'Le prix de vente doit être au moins 0.',
        ];
    }
}
