<?php

namespace App\Http\Controllers;
use App\Http\Requests\UploadAtomRequest;
use App\Services\AtomFileUploadService;
use App\Services\AtomDataExtractionService;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
class filecontroller extends Controller
{
    // Inyectamos el FormRequest y nuestro Service
    public function upload(UploadAtomRequest $request, AtomFileUploadService $fileService)
    {
        // 1. Delegar: Pasamos el archivo ya validado por el Request al Servicio
        $result = $fileService->upload($request->file('atom_file'));
        // 2. Responder: Evaluamos qué nos dijo el servicio y redirigimos
        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }
        return redirect()->back()->with('success', $result['message']);
    }



    public function updateDatabase(AtomDataExtractionService $extractionService)
    {
        $fileName = 'atom_file.atom';
        
        $result = $extractionService->processAndSave($fileName);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }

}