<?php

namespace App\Services\Inventario;

use App\Models\Inventario\Categoria;
use App\Repositories\Inventario\CategoriaRepository;
use Illuminate\Support\Facades\DB;

class CategoriaService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly CategoriaRepository $repository
    ) {
    }

    /**
     * Lista categorías.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Obtiene una categoría.
     */
    public function show(int $id): Categoria
    {
        return $this->repository->findById($id);
    }

    /**
     * Registra una categoría.
     */
    public function store(array $data): Categoria
    {
        return DB::transaction(function () use ($data) {

            $data['estado_registro'] = 'A';

            return $this->repository->create($data);

        });
    }

    /**
     * Actualiza una categoría.
     */
    public function update(Categoria $categoria, array $data): Categoria
    {
        return DB::transaction(function () use ($categoria, $data) {

            return $this->repository->update($categoria, $data);

        });
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(Categoria $categoria): bool
    {
        return DB::transaction(function () use ($categoria) {

            return $this->repository->delete($categoria);

        });
    }
}