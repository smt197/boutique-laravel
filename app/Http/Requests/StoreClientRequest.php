<?php

namespace App\Http\Requests;
use App\Rules\TelephoneRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\StatusResponseEnum;
use App\Rules\CustomPasswordRule;


class StoreClientRequest extends FormRequest
{
    use RestResponseTrait;
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
        $rules = [
            'surname' => ['required', 'string', 'max:255','unique:clients,surname'],
            'adresse' => ['string', 'max:255'],
            'telephone' => ['required',new TelephoneRule()],
            'categorie_id' => 'required|integer|exists:categories_clients,id',
            'max_montant' => 'nullable|numeric',

            'user' => ['sometimes','array'],
            'user.nom' => ['required_with:user','string'],
            'user.prenom' => ['required_with:user','string'],
            'user.login' => ['required_with:user','string'],
            'user.password' => ['required_with:user','confirmed'],
            'user.photo' => ['required_with:user','image', 'mimes:jpeg,png,jpg'],
            // 'user.role' => ['required_with:user','array'],
            'user.role_id' => ['required_with:user.role_id','integer'],
        ];

        return $rules;
    }

    function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.", 
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StatusResponseEnum::ECHEC,404));
    }
}
