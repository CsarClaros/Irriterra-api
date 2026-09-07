<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreProductoDocumentoRequest;
use App\Http\Requests\Inventario\UpdateProductoDocumentoRequest;
use App\Http\Resources\Inventario\ProductoDocumentoCollection;
use App\Http\Resources\Inventario\ProductoDocumentoResource;
use App\Models\Inventario\ProductoDocumento;
use App\Services\Inventario\ProductoDocumentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProductoDocumentoController
    extends Controller
{

    public function __construct(
        private readonly ProductoDocumentoService $service
    )
    {
    }


    public function index(
        Request $request
    ): ProductoDocumentoCollection
    {

        return new ProductoDocumentoCollection(

            $this->service
                ->index(
                    $request->boolean(
                        'incluir_inactivos'
                    )
                )

        );

    }


    public function store(
        StoreProductoDocumentoRequest $request
    ): JsonResponse
    {

        $documento =
            $this->service
                ->store(
                    $request->validated()
                );


        return response()->json(

            new ProductoDocumentoResource(
                $documento
            ),

            201

        );

    }


    public function show(
        ProductoDocumento $productoDocumento
    ): ProductoDocumentoResource
    {

        return new ProductoDocumentoResource(

            $this->service
                ->show(
                    $productoDocumento
                        ->id_producto_documento
                )

        );

    }


    public function update(
        UpdateProductoDocumentoRequest $request,
        ProductoDocumento              $productoDocumento
    ): JsonResponse
    {

        $documento =
            $this->service
                ->update(

                    $productoDocumento,

                    $request->validated()

                );


        return response()->json(

            new ProductoDocumentoResource(
                $documento
            ),

            200

        );

    }


    public function destroy(
        ProductoDocumento $productoDocumento
    ): JsonResponse
    {

        $this->service
            ->destroy(
                $productoDocumento
            );


        return response()->json([

            'message' =>
                'Documento desactivado correctamente.'

        ]);

    }


    public function reactivate(
        ProductoDocumento $productoDocumento
    ): JsonResponse
    {

        $this->service
            ->reactivate(
                $productoDocumento
            );


        return response()->json([

            'message' =>
                'Documento reactivado correctamente.'

        ]);

    }

}
