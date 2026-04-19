<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ProductoRepository;

class ProductoController extends Controller
{
    protected $productoRepository;

    public function __construct(ProductoRepository $productoRepository)
    {
        $this->productoRepository = $productoRepository;
    }
    public function index()
    {
        $productos = $this->productoRepository->obtenerCatalogo();

        return view('home', compact('productos'));
        //ok funcionando
    }
}
