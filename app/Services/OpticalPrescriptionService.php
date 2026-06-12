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

    public function analyze(string $base64Data, string $mimeType): array
    {
        $prompt = $this->buildPrompt();
        $response = $this->gemini->analizarImagen($base64Data, $mimeType, $prompt);
        $rawText = $this->gemini->extractTextFromResponse($response);

        if (!$rawText) {
            Log::error('Gemini returned no text', ['response' => $response]);
            throw new RuntimeException('El análisis no generó resultados. Inténtalo de nuevo.');
        }

        $prescription = $this->parseJson($rawText);

        $this->validatePrescription($prescription);

        $prescription = $this->normalize($prescription);

        return $prescription;
    }

    protected function buildPrompt(): string
    {
        $systemPrompt = file_get_contents($this->promptPath);
        $schema = file_get_contents($this->schemaPath);

        $schemaArray = json_decode($schema, true);
        if (!$schemaArray) {
            throw new RuntimeException('No se pudo cargar el esquema de receta óptica.');
        }

        $prompt = $systemPrompt . "\n\n";
        $prompt .= "Official JSON Schema:\n```json\n" . json_encode($schemaArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```\n\n";
        $prompt .= "Fill this schema with the optical values found in the prescription. ";
        $prompt .= "Return ONLY the filled JSON, nothing else.";

        return $prompt;
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
