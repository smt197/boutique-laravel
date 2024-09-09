<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\StateEnum;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'libelle' => 'required|string|unique:articles,libelle|max:255',
            'reference' => 'required|string|unique:articles,reference|max:255',
            'prix' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.string' => 'Le libellé doit contenir uniquement des lettres.',
            'libelle.unique' => 'Ce libellé est déjà utilisé.',
            'reference.unique' => 'Cette référence est déjà utilisé.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre valide.',
            'quantite.required' => 'La quantité en stock est obligatoire.',
            'quantite.integer' => 'La quantité en stock doit être un nombre entier.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'echec',
            'data' => $validator->errors(),
            'message' => 'Validation échouée'
        ], 422));
    }
}
