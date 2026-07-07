<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// app/Http/Requests/StoreModuleRequest.php
class StoreModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'categorie' => 'required|in:meuble_bas,meuble_haut,colonne,plan_travail,evier,robinetterie,electromenager',
            'largeur_cm' => 'required|integer|min:0',
            'hauteur_cm' => 'nullable|integer|min:0',
            'profondeur_cm' => 'nullable|integer|min:0',
            'prix_base' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validation image
        ];
    }
}

// app/Http/Requests/StoreMateriauRequest.php
class StoreMateriauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:couleur,finition,poignee,materiau',
            'supplement_prix' => 'required|numeric|min:0',
        ];
    }
}
