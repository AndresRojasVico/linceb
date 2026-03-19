<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAtomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permitir a cualquier usuario autenticado (o aplicar lógica de roles aquí)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Validamos que sea un archivo obligatorio y podemos usar reglas nativas
        return [
            'atom_file' => 'required|file'
        ];
    }
    public function messages()
    {
        return [
            'atom_file.required' => 'El archivo no se adjuntó.',
            'atom_file.file' => 'El archivo subido no es válido.'
        ];
    }
}
