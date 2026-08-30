<?php

namespace App\Services\Organizacion;

use App\Models\Organizacion\Sucursal;
use App\Repositories\Organizacion\SucursalRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SucursalService
{
    /**
     * Repositorio del módulo.
     */
    protected SucursalRepository $sucursalRepository;

    /**
     * Constructor.
     */
    public function __construct(SucursalRepository $sucursalRepository)
    {
        $this->sucursalRepository = $sucursalRepository;
    }

    /**
     * Obtener todas las sucursales.
     */
    public function all(): Collection
    {
        return $this->sucursalRepository->all();
    }

    /**
     * Buscar una sucursal.
     */
    public function find(int $id): ?Sucursal
    {
        return $this->sucursalRepository->find($id);
    }

    /**
     * Registrar sucursal.
     */
    public function create(array $data): Sucursal
    {
        return DB::transaction(function () use ($data) {

            $data['estado_registro'] = 'A';

            return $this->sucursalRepository->create($data);

            /*
            |--------------------------------------------------------------------------
            | Auditoría futura
            |--------------------------------------------------------------------------
            */

            return $sucursal;

        });
    }

    /**
     * Actualizar sucursal.
     */
    public function update(Sucursal $sucursal, array $data): Sucursal
    {
        return DB::transaction(function () use ($sucursal, $data) {

            $sucursal = $this->sucursalRepository->update($sucursal, $data);

            /*
            |--------------------------------------------------------------------------
            | Auditoría futura
            |--------------------------------------------------------------------------
            */

            return $sucursal;

        });
    }

    /**
     * Eliminación lógica.
     */
    public function delete(Sucursal $sucursal): bool
    {
        return DB::transaction(function () use ($sucursal) {

            /*
            |--------------------------------------------------------------------------
            | Validaciones futuras
            |--------------------------------------------------------------------------
            |
            | No eliminar si existen usuarios asociados.
            |
            */

            return $this->sucursalRepository->delete($sucursal);

        });
    }
}