<?php

namespace App\Http\Resources\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UsuarioResource extends JsonResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_usuario' => $this->id_usuario,

            'id_rol' => $this->id_rol,

            'id_sucursal' => $this->id_sucursal,

            'ci' => $this->ci,

            'usuario' => $this->usuario,

            'nombre' => $this->nombre,

            'apellido_paterno' => $this->apellido_paterno,

            'apellido_materno' => $this->apellido_materno,

            'correo' => $this->correo,

            'telefono' => $this->telefono,

            'direccion' => $this->direccion,

            'foto' =>

                $this->foto

                    ? Storage::disk(
                    'public'
                )->url(
                    $this->foto
                )

                    : null,

            'ultimo_acceso' => $this->ultimo_acceso,

            'estado_registro' => $this->estado_registro,

            'rol' =>

                $this->whenLoaded(
                    'rol',
                    function () {

                        return [

                            'id_rol' =>
                                $this->rol
                                    ->id_rol,

                            'nombre' =>
                                $this->rol
                                    ->nombre,

                            'nivel' =>
                                $this->rol
                                    ->nivel

                        ];

                    }
                ),

            'permisos' =>
                $this->when(
                    $this->relationLoaded('rol')
                    &&
                    $this->rol
                    &&
                    $this->rol->relationLoaded(
                        'permisos'
                    ),

                    function () {
                        return $this->rol
                            ->permisos
                            ->pluck('nombre')
                            ->values()
                            ->all();
                    }
                ),

            'sucursal' => $this->whenLoaded('sucursal', function () {

                return [

                    'id_sucursal' => $this->sucursal->id_sucursal,

                    'nombre' => $this->sucursal->nombre

                ];

            }),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}
