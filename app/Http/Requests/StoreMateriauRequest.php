<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
