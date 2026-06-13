<?php

namespace App\Http\Controllers;

use App\Services\OpticalPrescriptionService;
use App\Repositories\OcupacionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PrescriptionController extends Controller
{
    protected OpticalPrescriptionService $prescriptionService;
    protected OcupacionRepository $ocupacionRepository;

    public function __construct(
        OpticalPrescriptionService $prescriptionService,
        OcupacionRepository $ocupacionRepository
    ) {
        $this->prescriptionService = $prescriptionService;
        $this->ocupacionRepository = $ocupacionRepository;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpeg,jpg,png,webp,pdf',
            'id_ocupacion' => 'required|integer',
            'fecha_nacimiento' => 'required|date',
        ]);

        $uploadedFile = $request->file('file');
        $idOcupacion = $request->input('id_ocupacion');
        $fechaNacimiento = $request->input('fecha_nacimiento');

        $base64Data = base64_encode(file_get_contents($uploadedFile->getRealPath()));
        $mimeType = $uploadedFile->getMimeType();

        $ocupacion = $this->ocupacionRepository->findById($idOcupacion);
        $nombreOcupacion = $ocupacion ? $ocupacion->nombre : 'No especificada';

        $edad = Carbon::parse($fechaNacimiento)->age;

        $patientContext = [
            'ocupacion' => $nombreOcupacion,
            'fecha_nacimiento' => $fechaNacimiento,
            'edad' => $edad,
        ];

        try {
            $prescription = $this->prescriptionService->analyze($base64Data, $mimeType, $patientContext);

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
