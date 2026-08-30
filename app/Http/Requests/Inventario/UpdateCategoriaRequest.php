<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\Categoria;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends BaseRequest
{
    /**
     * Reglas para actualizar una categoría.
     */
    public function rules(): array
    {
        $categoria = $this->route('categoria');

        $idCategoria = $categoria instanceof Categoria
            ? $categoria->id_categoria
            : ($categoria ?? $this->route('id'));

        return [

            'nombre' => [

                'required',

                'string',

                'max:150',

                Rule::unique('categoria', 'nombre')
                    ->ignore($idCategoria, 'id_categoria')

            ],

            'descripcion' => [

                'nullable',

                'string',

                'max:255'

            ],

            'observaciones' => [

                'nullable',

                'string'

            ]

        ];
    }
}