<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Exception;

class AtomFileUploadService
{
    /**
     * Sube el archivo si cumple los requisitos y devuelve un array con el estado.
     */
    public function upload(UploadedFile $file): array
    {
        try {
            // Validamos manualmente la extensión
            if (strtolower($file->getClientOriginalExtension()) !== 'atom') {
                return ['success' => false, 'message' => 'Extensión no permitida. El archivo debe ser .atom.'];
            }

            $filename = 'atom_file.atom';

            // Subimos el fichero
            $file->storeAs('', $filename, 'files');

            return ['success' => true, 'message' => 'Archivo subido exitosamente.'];

        } catch (Exception $e) {
            // Un Log aquí sería buena práctica: \Log::error('Error subiendo archivo: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Hubo un error interno al guardar el archivo.'];
        }
    }
}
