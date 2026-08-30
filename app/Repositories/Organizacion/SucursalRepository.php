<?php

namespace App\Repositories\Organizacion;

use App\Models\Organizacion\Sucursal;
use Illuminate\Database\Eloquent\Collection;

class SucursalRepository
{
    /**
     * Obtener todas las sucursales activas.
     */
    public function all(): Collection
    {
        return Sucursal::where('estado_registro', 'A')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Buscar una sucursal.
     */
    public function find(int $id): ?Sucursal
    {
        return Sucursal::find($id);
    }

    /**
     * Registrar sucursal.
     */
    public function create(array $data): Sucursal
    {
        return Sucursal::create($data);
    }

    /**
     * Actualizar sucursal.
     */
    public function update(Sucursal $sucursal, array $data): Sucursal
    {
        $sucursal->update($data);

        return $sucursal->fresh();
    }

    /**
     * Eliminación lógica.
     */
    public function delete(Sucursal $sucursal): bool
    {
        return $sucursal->update([
            'estado_registro' => 'I'
        ]);
    }
}