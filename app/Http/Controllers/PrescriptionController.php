<?php

namespace App\Http\Controllers;

use App\Services\OpticalPrescriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrescriptionController extends Controller
{
    protected OpticalPrescriptionService $prescriptionService;

    public function __construct(OpticalPrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpeg,jpg,png,webp,pdf',
        ]);

        $uploadedFile = $request->file('file');

        $base64Data = base64_encode(file_get_contents($uploadedFile->getRealPath()));
        $mimeType = $uploadedFile->getMimeType();

        try {
            $prescription = $this->prescriptionService->analyze($base64Data, $mimeType);

            return response()->json([
                'success' => true,
                'data' => $prescription,
            ]);
        } catch (\RuntimeException $e) {
            Log::error('Prescripcion analysis failed', [
                'error' => $e->getMessage(),
                'mimeType' => $mimeType,
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
