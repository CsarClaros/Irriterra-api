<?php

namespace App\Services\Organizacion;

use App\Models\Organizacion\Empresa;
use App\Repositories\Organizacion\EmpresaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class EmpresaService
{
    /**
     * Repositorio del módulo Empresa.
     */
    protected EmpresaRepository $empresaRepository;

    /**
     * Constructor.
     */
    public function __construct(EmpresaRepository $empresaRepository)
    {
        $this->empresaRepository = $empresaRepository;
    }

    /**
     * Obtener listado de empresas.
     */
    public function all(): Collection
    {
        return $this->empresaRepository->all();
    }

    /**
     * Obtener empresa por ID.
     */
    public function find(int $id): ?Empresa
    {
        return $this->empresaRepository->find($id);
    }

    /**
     * Registrar empresa.
     */
    public function create(array $data): Empresa
    {
        return DB::transaction(function () use ($data) {

            $data['estado_registro'] = 'A';

            return $this->empresaRepository->create($data);
            /*
            |--------------------------------------------------------------------------
            | Auditoría (Futuro)
            |--------------------------------------------------------------------------
            |
            | Registrar creación de empresa.
            |
            */

            return $empresa;
        });
    }

    /**
     * Actualizar empresa.
     */
    public function update(Empresa $empresa, array $data): Empresa
    {
        return DB::transaction(function () use ($empresa, $data) {

            $empresa = $this->empresaRepository->update($empresa, $data);

            /*
            |--------------------------------------------------------------------------
            | Auditoría (Futuro)
            |--------------------------------------------------------------------------
            */

            return $empresa;
        });
    }

    /**
     * Eliminación lógica.
     */
    public function delete(Empresa $empresa): bool
    {
        return DB::transaction(function () use ($empresa) {

            /*
            |--------------------------------------------------------------------------
            | Validaciones futuras
            |--------------------------------------------------------------------------
            |
            | Verificar si existen sucursales activas.
            |
            */

            return $this->empresaRepository->delete($empresa);
        });
    }
}
