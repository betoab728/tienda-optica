<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OcupacionRepository
{
    public function obtenerOcupaciones()
    {
        try {
            $resultados = DB::select('EXEC ListarOcupaciones');

            return array_map(function ($row) {
                return (object) [
                    'id' => $row->idocupacion,
                    'nombre' => $row->descripción ?? ($row->descripcion ?? ''),
                    'estado' => $row->estado ?? null,
                ];
            }, $resultados);
        } catch (\Exception $e) {
            Log::error('Error al obtener ocupaciones: ' . $e->getMessage());
            return [];
        }
    }
}
