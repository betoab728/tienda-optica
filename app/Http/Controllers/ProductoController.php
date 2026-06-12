<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ProductoRepository;
use App\Repositories\OcupacionRepository;

class ProductoController extends Controller
{
    protected $productoRepository;
    protected $ocupacionRepository;

    public function __construct(ProductoRepository $productoRepository, OcupacionRepository $ocupacionRepository)
    {
        $this->productoRepository = $productoRepository;
        $this->ocupacionRepository = $ocupacionRepository;
    }
    public function index()
    {
        $productos = $this->productoRepository->obtenerCatalogo();
        $ocupaciones = $this->ocupacionRepository->obtenerOcupaciones();

        return view('home', compact('productos', 'ocupaciones'));
    }
    public function show($id)
    {
        $producto = $this->productoRepository->obtenerPorId($id);

        if (!$producto) {
            abort(404);
        }

        return view('producto.detalle', compact('producto'));
    }
}
