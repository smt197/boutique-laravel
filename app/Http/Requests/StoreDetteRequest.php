<?php
namespace App\Http\Requests;

use App\Enums\StatusResponseEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreDetteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'date_echeance' => 'nullable',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|numeric',
            'articles.*.prixVente' => 'required|numeric',
            'paiement.montant' => 'nullable|numeric|max:' . $this->input('montant'),
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => StatusResponseEnum::ECHEC,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
