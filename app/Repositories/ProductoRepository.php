<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Env;

class ProductoRepository
{
  
    public function obtenerCatalogo()
    {
        $productos = DB::table('optica.producto as p')
            ->join('optica.familia as f', 'p.idfamilia', '=', 'f.idfamilia')
            ->join('optica.modelo as m', 'p.idmodelo', '=', 'm.idmodelo')
            ->join('optica.marca as ma', 'm.idmarca', '=', 'ma.idmarca')
            ->join('optica.disenio as d', 'p.iddisenio', '=', 'd.iddisenio')
            ->where('p.venta_online', 1)
            ->select(
                'p.idproducto',
                'p.codigo',
                'p.color',
                'p.p_venta as precioVenta',
                'p.p_minimo as precioMinimo',
                'f.nombre as familia',
                'm.nombre as modelo',
                'ma.nombre as marca',
                'd.descripcion'
            )
            ->limit(20) // puedes cambiar luego por paginación
            ->get();

        // Agregar URL de imagen (tu CDN con Nginx)
        foreach ($productos as $p) {
            $p->imagen = env('IMAGES_URL') . "/{$p->idproducto}.jpg";
        }

        return $productos;
    }
    public function obtenerPorId($id)
{
    $p = DB::table('optica.producto as p')
        ->join('optica.familia as f', 'p.idfamilia', '=', 'f.idfamilia')
        ->join('optica.modelo as m', 'p.idmodelo', '=', 'm.idmodelo')
        ->join('optica.marca as ma', 'm.idmarca', '=', 'ma.idmarca')
        ->join('optica.disenio as d', 'p.iddisenio', '=', 'd.iddisenio')
        ->where('p.idproducto', $id)
        ->select(
            'p.idproducto',
            'p.codigo',
            'p.color',
            'p.p_venta as precioVenta',
            'p.p_minimo as precioMinimo',
            'f.nombre as familia',
            'm.nombre as modelo',
            'ma.nombre as marca',
            'd.descripcion'
        )
        ->first();

    if (!$p) return null;

    //  GENERAR ARRAY DE IMÁGENES
    $imagenes = [];

    for ($i = 1; $i <= 5; $i++) {
        $imagenes[] = env('IMAGES_URL') . "/{$p->idproducto}-" . str_pad($i, 2, '0', STR_PAD_LEFT) . ".jpg";
    }

    $p->imagenes = $imagenes;

    return $p;
}
}
