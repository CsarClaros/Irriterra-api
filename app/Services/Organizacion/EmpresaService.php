<?php

namespace App\Services\Organizacion;

use App\Models\Organizacion\Empresa;
use App\Repositories\Organizacion\EmpresaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
    public function update(
        Empresa $empresa,
        array $data
    ): Empresa {

        $rutaLogoAnterior =
            $empresa->logo;


        $rutaLogoNueva =
            null;


        try {

            $empresaActualizada =
                DB::transaction(
                    function () use (
                        $empresa,
                        $data,
                        &$rutaLogoNueva
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | Nuevo logo
                        |--------------------------------------------------------------------------
                        */

                        if (
                            isset(
                                $data['logo']
                            )
                            &&
                            $data['logo']
                            instanceof UploadedFile
                        ) {

                            $rutaLogoNueva =
                                $data['logo']
                                    ->store(
                                        'empresa',
                                        'public'
                                    );


                            $data['logo'] =
                                $rutaLogoNueva;

                        } else {

                            /*
                             * Si no se recibió un archivo,
                             * conservar logo existente.
                             */

                            unset(
                                $data['logo']
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Actualizar
                        |--------------------------------------------------------------------------
                        */

                        return $this
                            ->empresaRepository
                            ->update(
                                $empresa,
                                $data
                            );

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Eliminar logo anterior
            |--------------------------------------------------------------------------
            |
            | Se hace después de que la
            | actualización de BD terminó
            | correctamente.
            |
            */

            if (
                $rutaLogoNueva
                &&
                $rutaLogoAnterior
                &&
                str_starts_with(
                    $rutaLogoAnterior,
                    'empresa/'
                )
            ) {

                Storage::disk(
                    'public'
                )
                    ->delete(
                        $rutaLogoAnterior
                    );

            }


            return $empresaActualizada;

        } catch (
        Throwable $exception
        ) {

            /*
             * La BD falló:
             * eliminar nuevo archivo.
             */

            if (
                $rutaLogoNueva
            ) {

                Storage::disk(
                    'public'
                )
                    ->delete(
                        $rutaLogoNueva
                    );

            }


            throw $exception;

        }

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

    public function store(
        array $data
    ): Empresa {

        $rutaLogoNueva =
            null;


        try {

            return DB::transaction(
                function () use (
                    $data,
                    &$rutaLogoNueva
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Logo
                    |--------------------------------------------------------------------------
                    */

                    if (
                        isset(
                            $data['logo']
                        )
                        &&
                        $data['logo']
                        instanceof UploadedFile
                    ) {

                        $rutaLogoNueva =
                            $data['logo']
                                ->store(
                                    'empresa',
                                    'public'
                                );


                        $data['logo'] =
                            $rutaLogoNueva;

                    } else {

                        unset(
                            $data['logo']
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Registrar empresa
                    |--------------------------------------------------------------------------
                    */

                    return $this
                        ->repository
                        ->create(
                            $data
                        );

                }
            );

        } catch (
        Throwable $exception
        ) {

            /*
             * Si la BD falla después
             * de almacenar el archivo,
             * eliminamos el archivo nuevo.
             */

            if (
                $rutaLogoNueva
            ) {

                Storage::disk(
                    'public'
                )
                    ->delete(
                        $rutaLogoNueva
                    );

            }


            throw $exception;

        }

    }


}
