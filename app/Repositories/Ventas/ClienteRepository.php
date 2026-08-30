<?php

namespace App\Repositories\Ventas;

use App\Models\Ventas\Cliente;
use Illuminate\Database\Eloquent\Collection;

class ClienteRepository
{
    /**
     * Obtiene todos los clientes activos.
     */
    public function getAll(): Collection
    {
        return Cliente::where(
            'estado_registro',
            'A'
        )
            ->orderBy(
                'nombre_razon_social'
            )
            ->get();
    }

    /**
     * Busca un cliente activo por su ID.
     */
    public function findById(
        int $id
    ): Cliente {

        return Cliente::where(
            'estado_registro',
            'A'
        )
            ->findOrFail($id);
    }

    /**
     * Busca y bloquea un cliente para actualización.
     */
    public function findByIdForUpdate(
        int $id
    ): Cliente {

        return Cliente::where(
            'estado_registro',
            'A'
        )
            ->lockForUpdate()
            ->findOrFail($id);
    }

    /**
     * Registra un cliente.
     */
    public function create(
        array $data
    ): Cliente {

        return Cliente::create($data);
    }

    /**
     * Actualiza un cliente.
     */
    public function update(
        Cliente $cliente,
        array $data
    ): Cliente {

        $cliente->update($data);

        return $cliente->fresh();
    }

    /**
     * Realiza la eliminación lógica.
     */
    public function delete(
        Cliente $cliente
    ): bool {

        return $cliente->update([

            'estado_registro' =>
                'I'

        ]);
    }
}