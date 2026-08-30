<?php

namespace App\Services\Ventas;

use App\Models\Ventas\Cliente;
use App\Repositories\Ventas\ClienteRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ClienteService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ClienteRepository $repository
    ) {
    }

    /**
     * Lista los clientes activos.
     */
    public function index(): Collection
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra un cliente activo.
     */
    public function show(
        int $id
    ): Cliente {

        return $this->repository->findById(
            $id
        );
    }

    /**
     * Registra un cliente.
     */
    public function store(
        array $data
    ): Cliente {

        return DB::transaction(
            function () use ($data) {

                $data['estado_registro'] =
                    'A';

                $data['tipo_cliente'] =
                    $data['tipo_cliente']
                        ?? Cliente::PERSONA;

                return $this->repository->create(
                    $data
                );

            }
        );
    }

    /**
     * Actualiza un cliente.
     */
    public function update(
        Cliente $cliente,
        array $data
    ): Cliente {

        return DB::transaction(
            function () use (
                $cliente,
                $data
            ) {

                $cliente =
                    $this->repository
                        ->findByIdForUpdate(

                            $cliente->id_cliente

                        );

                unset(
                    $data['estado_registro'],
                    $data['usuario_creacion']
                );

                return $this->repository->update(

                    $cliente,

                    $data

                );

            }
        );
    }

    /**
     * Elimina lógicamente un cliente.
     */
    public function destroy(
        Cliente $cliente
    ): bool {

        return DB::transaction(
            function () use ($cliente) {

                $cliente =
                    $this->repository
                        ->findByIdForUpdate(

                            $cliente->id_cliente

                        );

                return $this->repository->delete(
                    $cliente
                );

            }
        );
    }
}