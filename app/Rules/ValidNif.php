<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidNif implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Expresión regular para NIF/CIF/NIE básico en España
        // (Ajusta el regex según tus necesidades específicas)
        $regex = '/^[ABCDEFGHJKLMNPQRSUVW][0-9]{7}[0-9A-J]$/';

        if (!preg_match($regex, $value)) {
            $fail('El formato del NIF no es válido.');
        }

        // Aquí podrías añadir el cálculo del dígito de control si lo deseas
    }
}
