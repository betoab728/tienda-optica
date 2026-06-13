<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpticalPrescriptionService
{
    protected GeminiService $gemini;
    protected string $promptPath;
    protected string $schemaPath;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
        $this->promptPath = resource_path('prompts/analizar-receta.md');
        $this->schemaPath = resource_path('json/receta-optica.json');
    }

    public function analyze(string $base64Data, string $mimeType, array $patientContext = []): array
    {
        $prompt = $this->buildPrompt($patientContext);
        $response = $this->gemini->analizarImagen($base64Data, $mimeType, $prompt);
        $rawText = $this->gemini->extractTextFromResponse($response);

        if (!$rawText) {
            Log::error('Gemini returned no text', ['response' => $response]);
            throw new RuntimeException('El análisis no generó resultados. Inténtalo de nuevo.');
        }

        $prescription = $this->parseJson($rawText);

        $this->validatePrescription($prescription);

        $prescription = $this->normalize($prescription);

        if (!empty($patientContext)) {
            $prescription = $this->overwritePatientFields($prescription, $patientContext);
        }

        return $prescription;
    }

 protected function buildPrompt(array $patientContext = []): string
{
    $systemPrompt = file_get_contents($this->promptPath);
    $schema = file_get_contents($this->schemaPath);

    $schemaArray = json_decode($schema, true);

    if (!$schemaArray) {
        throw new RuntimeException('No se pudo cargar el esquema de receta óptica.');
    }

    $prompt = $systemPrompt . "\n\n";

    if (!empty($patientContext)) {

        $prompt .= "--- PATIENT CONTEXT ---\n\n";

        $prompt .= "The following information is known about the patient:\n";
        $prompt .= "- Occupation: " . ($patientContext['ocupacion'] ?? 'Not specified') . "\n";
        $prompt .= "- Age: " . ($patientContext['edad'] ?? 'Not specified') . "\n";
        $prompt .= "- Date of Birth: " . ($patientContext['fecha_nacimiento'] ?? 'Not specified') . "\n\n";

        $prompt .= "Use this information ONLY to improve these fields:\n";
        $prompt .= "- analisis_ia.interpretacion_usuario\n";
        $prompt .= "- analisis_ia.recomendacion_general\n";
        $prompt .= "- analisis_ia.requiere_cita\n\n";

        $prompt .= "Occupation and age may be used to personalize the interpretation and recommendation.\n";
        $prompt .= "Examples include office work, prolonged computer use, reading activities, driving, precision work, or similar visual demands.\n\n";

        $prompt .= "DO NOT infer or generate prescription optical values from age or occupation.\n";
        $prompt .= "Prescription values (esfera, cilindro, eje, dip, av, add, prisma, cb, diametro, etc.) must come EXCLUSIVELY from the uploaded prescription document.\n\n";

        $prompt .= "Age alone does NOT justify diagnosing presbyopia.\n";
        $prompt .= "Addition (ADD) values alone do NOT automatically mean presbyopia.\n";
        $prompt .= "Presbyopia should only be mentioned when the complete prescription clearly supports that conclusion.\n\n";

        $prompt .= "DO NOT populate paciente.ocupacion, paciente.fecha_nacimiento, or paciente.edad. The backend will populate those fields.\n\n";
    }

    $prompt .= "Official JSON Schema:\n";
    $prompt .= json_encode(
        $schemaArray,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
    $prompt .= "\n\n";

    $prompt .= "Additional extraction rules:\n";
    $prompt .= "- If a value is clearly visible, extract it exactly.\n";
    $prompt .= "- If a value is missing, unreadable, or not present, return null.\n";
    $prompt .= "- Never guess or invent optical values.\n";
    $prompt .= "- Partial extraction is preferred over invented values.\n";
    $prompt .= "- If the prescription is partially readable, extract only the values that can be identified confidently.\n\n";

    $prompt .= "Fill this schema with the optical values found in the prescription.\n";
    $prompt .= "Return ONLY the completed JSON object.\n";
    $prompt .= "Do not return markdown.\n";
    $prompt .= "Do not return explanations.\n";
    $prompt .= "Do not return text before or after the JSON.\n";

    return $prompt;
}

    protected function overwritePatientFields(array $prescription, array $patientContext): array
    {
        $prescription['paciente']['ocupacion'] = $patientContext['ocupacion'] ?? null;
        $prescription['paciente']['fecha_nacimiento'] = $patientContext['fecha_nacimiento'] ?? null;
        $prescription['paciente']['edad'] = $patientContext['edad'] ?? null;

        return $prescription;
    }

    protected function parseJson(string $rawText): array
    {
        $cleaned = trim($rawText);

        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
        }

        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON parse error', [
                'error' => json_last_error_msg(),
                'raw' => mb_substr($cleaned, 0, 500),
            ]);
            throw new RuntimeException('El resultado del análisis no es válido. Inténtalo de nuevo.');
        }

        return $data;
    }

    protected function validatePrescription(array $data): void
    {
        $schemaJson = file_get_contents($this->schemaPath);
        $schema = json_decode($schemaJson, true);

        $this->mergeDefaults($data, $schema);

        if (!isset($data['vision_lejos'], $data['vision_cerca'])) {
            throw new RuntimeException('La receta analizada no contiene los campos esperados.');
        }
    }

    protected function mergeDefaults(array &$data, array $schema): void
    {
        foreach ($schema as $key => $defaultValue) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = $defaultValue;
            } elseif (is_array($defaultValue) && is_array($data[$key])) {
                $this->mergeDefaults($data[$key], $defaultValue);
            }
        }
    }

    protected function normalize(array $prescription): array
    {
        foreach (['vision_lejos', 'vision_cerca'] as $visionType) {
            if (isset($prescription[$visionType])) {
                foreach (['od', 'oi'] as $eye) {
                    if (isset($prescription[$visionType][$eye])) {
                        foreach (['esfera', 'cilindro', 'eje', 'dip', 'av'] as $field) {
                            if (isset($prescription[$visionType][$eye][$field]) && $prescription[$visionType][$eye][$field] === '') {
                                $prescription[$visionType][$eye][$field] = null;
                            }
                        }
                    }
                }
            }
        }

        if (isset($prescription['paciente'])) {
            foreach (['edad'] as $field) {
                if (isset($prescription['paciente'][$field]) && $prescription['paciente'][$field] === '') {
                    $prescription['paciente'][$field] = null;
                }
            }
        }

        if (isset($prescription['analisis_ia'])) {
            $boolFields = ['requiere_multifocal', 'requiere_alto_indice', 'requiere_reduccion_diametro', 'requiere_cita'];
            foreach ($boolFields as $field) {
                if (isset($prescription['analisis_ia'][$field])) {
                    $prescription['analisis_ia'][$field] = (bool) $prescription['analisis_ia'][$field];
                }
            }

            if (!isset($prescription['analisis_ia']['nivel_complejidad'])) {
                $prescription['analisis_ia']['nivel_complejidad'] = 'estandar';
            }
        }

        return $prescription;
    }
}
