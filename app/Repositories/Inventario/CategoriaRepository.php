<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\Categoria;

class CategoriaRepository
{
    /**
     * Lista categorías activas.
     */
    public function getAll()
    {
        return Categoria::where('estado_registro', 'A')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(int $id): Categoria
    {
        return Categoria::findOrFail($id);
    }

    /**
     * Registra una categoría.
     */
    public function create(array $data): Categoria
    {
        return Categoria::create($data);
    }

    /**
     * Actualiza una categoría.
     */
    public function update(Categoria $categoria, array $data): Categoria
    {
        $categoria->update($data);

        // return $categoria->fresh();
        return $categoria;
    }

    /**
     * Eliminación lógica.
     */
    public function delete(Categoria $categoria): bool
    {
        return $categoria->update([

            'estado_registro' => 'I'

        ]);
    }
}