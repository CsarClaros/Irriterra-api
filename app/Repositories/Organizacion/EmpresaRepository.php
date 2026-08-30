<?php

namespace App\Repositories\Organizacion;

use App\Models\Organizacion\Empresa;
use Illuminate\Database\Eloquent\Collection;

class EmpresaRepository
{
    /**
     * Obtener todas las empresas activas.
     */
    public function all(): Collection
    {
        return Empresa::where('estado_registro', 'A')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Buscar una empresa por su ID.
     */
    public function find(int $id): ?Empresa
    {
        return Empresa::find($id);
    }

    /**
     * Registrar una empresa.
     */
    public function create(array $data): Empresa
    {
        return Empresa::create($data);
    }

    /**
     * Actualizar una empresa.
     */
    public function update(Empresa $empresa, array $data): Empresa
    {
        $empresa->update($data);

        return $empresa->fresh();
    }

    /**
     * Eliminación lógica.
     */
    public function delete(Empresa $empresa): bool
    {
        return $empresa->update([
            'estado_registro' => 'I'
        ]);
    }
}