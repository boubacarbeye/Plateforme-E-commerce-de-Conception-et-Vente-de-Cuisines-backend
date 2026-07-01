<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// app/Http/Requests/StoreProjetRequest.php
class StoreProjetRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom' => 'nullable|string|max:255',
            'longueur_cm' => 'required|integer|min:100',
            'largeur_cm' => 'required|integer|min:100',
            'hauteur_cm' => 'required|integer|min:200',
            'forme' => 'required|in:lineaire,en_L', // RG-01
        ];
    }
}