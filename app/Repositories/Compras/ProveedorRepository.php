<?php

namespace App\Repositories\Compras;

use App\Models\Compras\Proveedor;
use Illuminate\Database\Eloquent\Collection;

class ProveedorRepository
{
    /**
     * Obtiene todos los proveedores activos.
     */
    public function getAll(): Collection
    {
        return Proveedor::where(
            'estado_registro',
            'A'
        )
            ->orderBy(
                'nombre_razon_social'
            )
            ->get();
    }

    /**
     * Busca un proveedor activo por ID.
     */
    public function findById(
        int $id
    ): Proveedor {

        return Proveedor::where(
            'estado_registro',
            'A'
        )
            ->findOrFail($id);
    }

    /**
     * Busca y bloquea un proveedor.
     */
    public function findByIdForUpdate(
        int $id
    ): Proveedor {

        return Proveedor::where(
            'estado_registro',
            'A'
        )
            ->lockForUpdate()
            ->findOrFail($id);
    }

    /**
     * Registra un proveedor.
     */
    public function create(
        array $data
    ): Proveedor {

        return Proveedor::create($data);
    }

    /**
     * Actualiza un proveedor.
     */
    public function update(
        Proveedor $proveedor,
        array $data
    ): Proveedor {

        $proveedor->update($data);

        return $proveedor->fresh();
    }

    /**
     * Realiza la eliminación lógica.
     */
    public function delete(
        Proveedor $proveedor
    ): bool {

        return $proveedor->update([

            'estado_registro' => 'I'

        ]);
    }
}