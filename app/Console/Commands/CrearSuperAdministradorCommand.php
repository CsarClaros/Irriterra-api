<?php

namespace App\Console\Commands;

use App\Models\Organizacion\Sucursal;
use App\Models\Seguridad\Rol;
use App\Models\Seguridad\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RuntimeException;
use Throwable;


class CrearSuperAdministradorCommand
    extends Command
{

    protected $signature =
        'irriterra:crear-superadmin';


    protected $description =
        'Crea de forma segura el SuperAdministrador inicial de Irriterra.';


    public function handle(): int
    {

        /*
        |--------------------------------------------------------------------------
        | Rol SuperAdministrador
        |--------------------------------------------------------------------------
        */

        $rol =
            Rol::query()
                ->where(
                    'nombre',
                    Rol::SUPER_ADMINISTRADOR
                )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->first();


        if (
            !$rol
        ) {

            $this->error(
                'No existe un rol SuperAdministrador activo.'
            );


            $this->comment(
                'Ejecute primero ProductionSeeder.'
            );


            return self::FAILURE;

        }


        /*
        |--------------------------------------------------------------------------
        | Evitar creación inicial duplicada
        |--------------------------------------------------------------------------
        */

        $superAdministradorExistente =
            Usuario::query()
                ->where(
                    'id_rol',
                    $rol->id_rol
                )
                ->first();


        if (
            $superAdministradorExistente
        ) {

            $this->error(
                'Ya existe una cuenta con rol SuperAdministrador.'
            );


            $this->table(
                [
                    'Dato',
                    'Valor'
                ],
                [
                    [
                        'ID',
                        $superAdministradorExistente
                            ->id_usuario
                    ],
                    [
                        'Usuario',
                        $superAdministradorExistente
                            ->usuario
                    ],
                    [
                        'Estado',
                        $superAdministradorExistente
                            ->estado_registro
                    ]
                ]
            );


            $this->comment(
                'No se creó ninguna cuenta adicional.'
            );


            return self::FAILURE;

        }


        /*
        |--------------------------------------------------------------------------
        | Sucursal
        |--------------------------------------------------------------------------
        */

        $sucursales =
            Sucursal::query()
                ->where(
                    'estado_registro',
                    'A'
                )
                ->orderBy(
                    'id_sucursal'
                )
                ->get();


        if (
            $sucursales->isEmpty()
        ) {

            $this->error(
                'No existe ninguna sucursal activa.'
            );


            return self::FAILURE;

        }


        if (
            $sucursales->count()
            === 1
        ) {

            $sucursal =
                $sucursales->first();


            $this->info(
                'Sucursal asignada: '
                . $sucursal->nombre
            );

        } else {

            $opciones =
                $sucursales
                    ->mapWithKeys(
                        function (
                            Sucursal $sucursal
                        ) {

                            $etiqueta =
                                $sucursal->codigo
                                . ' | '
                                . $sucursal->nombre
                                . ' | '
                                . $sucursal->ciudad;


                            return [
                                (string)
                                $sucursal->id_sucursal
                                =>
                                    $etiqueta
                            ];

                        }
                    )
                    ->all();


            $seleccion =
                $this->choice(
                    'Seleccione la sucursal',
                    array_values(
                        $opciones
                    )
                );


            $idSucursal =
                array_search(
                    $seleccion,
                    $opciones,
                    true
                );


            $sucursal =
                $sucursales
                    ->firstWhere(
                        'id_sucursal',
                        (int)
                        $idSucursal
                    );


            if (
                !$sucursal
            ) {

                throw new RuntimeException(
                    'No fue posible determinar la sucursal seleccionada.'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Datos personales
        |--------------------------------------------------------------------------
        */

        $ci =
            $this->solicitarCampo(
                'CI',
                [
                    'required',
                    'string',
                    'max:20',
                    'unique:usuario,ci'
                ]
            );


        $nombre =
            $this->solicitarCampo(
                'Nombre(s)',
                [
                    'required',
                    'string',
                    'max:100'
                ]
            );


        $apellidoPaterno =
            $this->solicitarCampo(
                'Apellido paterno',
                [
                    'required',
                    'string',
                    'max:100'
                ]
            );


        $apellidoMaterno =
            $this->solicitarCampo(
                'Apellido materno (opcional)',
                [
                    'nullable',
                    'string',
                    'max:100'
                ],
                true
            );


        /*
        |--------------------------------------------------------------------------
        | Credenciales
        |--------------------------------------------------------------------------
        */

        $usuario =
            $this->solicitarUsuario();


        $correo =
            $this->solicitarCampo(
                'Correo electrónico (opcional)',
                [
                    'nullable',
                    'email',
                    'max:150',
                    'unique:usuario,correo'
                ],
                true
            );


        $telefono =
            $this->solicitarCampo(
                'Teléfono (opcional)',
                [
                    'nullable',
                    'string',
                    'max:20'
                ],
                true
            );


        $direccion =
            $this->solicitarCampo(
                'Dirección (opcional)',
                [
                    'nullable',
                    'string',
                    'max:255'
                ],
                true
            );


        $password =
            $this->solicitarPassword();


        /*
        |--------------------------------------------------------------------------
        | Confirmación
        |--------------------------------------------------------------------------
        */

        $this->newLine();


        $this->table(
            [
                'Dato',
                'Valor'
            ],
            [
                [
                    'Rol',
                    Rol::SUPER_ADMINISTRADOR
                ],
                [
                    'Sucursal',
                    $sucursal->nombre
                ],
                [
                    'CI',
                    $ci
                ],
                [
                    'Usuario',
                    $usuario
                ],
                [
                    'Nombre',
                    $nombre
                ],
                [
                    'Apellido paterno',
                    $apellidoPaterno
                ],
                [
                    'Apellido materno',
                    $apellidoMaterno
                    ?? '-'
                ],
                [
                    'Correo',
                    $correo
                    ?? '-'
                ],
                [
                    'Teléfono',
                    $telefono
                    ?? '-'
                ],
                [
                    'Dirección',
                    $direccion
                    ?? '-'
                ]
            ]
        );


        $this->warn(
            'Esta cuenta tendrá acceso técnico completo al ERP.'
        );


        if (
            !$this->confirm(
                '¿Desea crear el SuperAdministrador?',
                false
            )
        ) {

            $this->comment(
                'Creación cancelada.'
            );


            return self::SUCCESS;

        }


        /*
        |--------------------------------------------------------------------------
        | Crear usuario
        |--------------------------------------------------------------------------
        */

        try {

            $superAdministrador =
                DB::transaction(
                    function () use (
                        $rol,
                        $sucursal,
                        $ci,
                        $usuario,
                        $password,
                        $nombre,
                        $apellidoPaterno,
                        $apellidoMaterno,
                        $correo,
                        $telefono,
                        $direccion
                    ) {

                        /*
                         * Bloqueamos el rol durante esta
                         * operación para evitar dos
                         * inicializaciones simultáneas.
                         */

                        Rol::query()
                            ->where(
                                'id_rol',
                                $rol->id_rol
                            )
                            ->lockForUpdate()
                            ->firstOrFail();


                        /*
                         * Segunda comprobación dentro
                         * de la transacción.
                         */

                        $existe =
                            Usuario::query()
                                ->where(
                                    'id_rol',
                                    $rol->id_rol
                                )
                                ->exists();


                        if (
                            $existe
                        ) {

                            throw new RuntimeException(
                                'Ya existe un SuperAdministrador.'
                            );

                        }


                        return Usuario::create([

                            'id_rol' =>
                                $rol->id_rol,

                            'id_sucursal' =>
                                $sucursal->id_sucursal,

                            'ci' =>
                                $ci,

                            'usuario' =>
                                $usuario,

                            'password' =>
                                Hash::make(
                                    $password
                                ),

                            'nombre' =>
                                $nombre,

                            'apellido_paterno' =>
                                $apellidoPaterno,

                            'apellido_materno' =>
                                $apellidoMaterno,

                            'correo' =>
                                $correo,

                            'telefono' =>
                                $telefono,

                            'direccion' =>
                                $direccion,

                            'foto' =>
                                null,

                            'ultimo_acceso' =>
                                null,

                            'intentos_fallidos' =>
                                0,

                            'bloqueado_hasta' =>
                                null,

                            'estado_registro' =>
                                'A'

                        ]);

                    }
                );

        } catch (
        Throwable $exception
        ) {

            $this->error(
                'No se pudo crear el SuperAdministrador.'
            );


            $this->error(
                $exception->getMessage()
            );


            return self::FAILURE;

        }


        /*
        |--------------------------------------------------------------------------
        | Resultado
        |--------------------------------------------------------------------------
        */

        unset(
            $password
        );


        $this->newLine();


        $this->info(
            'SuperAdministrador creado correctamente.'
        );


        $this->table(
            [
                'Dato',
                'Valor'
            ],
            [
                [
                    'ID',
                    $superAdministrador
                        ->id_usuario
                ],
                [
                    'Usuario',
                    $superAdministrador
                        ->usuario
                ],
                [
                    'Rol',
                    Rol::SUPER_ADMINISTRADOR
                ],
                [
                    'Sucursal',
                    $sucursal->nombre
                ],
                [
                    'Estado',
                    $superAdministrador
                        ->estado_registro
                ]
            ]
        );


        $this->comment(
            'La contraseña no fue almacenada ni mostrada en texto plano.'
        );


        return self::SUCCESS;

    }


    /*
    |--------------------------------------------------------------------------
    | Campo genérico
    |--------------------------------------------------------------------------
    */

    private function solicitarCampo(
        string $pregunta,
        array  $reglas,
        bool   $opcional = false
    ): ?string
    {

        while (
        true
        ) {

            $valor =
                $this->ask(
                    $pregunta
                );


            if (
                $valor !== null
            ) {

                $valor =
                    trim(
                        (string)
                        $valor
                    );

            }


            if (
                $opcional
                &&
                (
                    $valor === null
                    ||
                    $valor === ''
                )
            ) {

                $valor =
                    null;

            }


            $validator =
                Validator::make(
                    [
                        'valor' =>
                            $valor
                    ],
                    [
                        'valor' =>
                            $reglas
                    ],
                    [],
                    [
                        'valor' =>
                            $pregunta
                    ]
                );


            if (
                $validator->passes()
            ) {

                return $valor;

            }


            foreach (
                $validator
                    ->errors()
                    ->all()
                as $error
            ) {

                $this->error(
                    $error
                );

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Usuario
    |--------------------------------------------------------------------------
    */

    private function solicitarUsuario(): string
    {

        while (
        true
        ) {

            $usuario =
                mb_strtolower(
                    trim(
                        (string)
                        $this->ask(
                            'Nombre de usuario'
                        )
                    ),
                    'UTF-8'
                );


            $validator =
                Validator::make(
                    [
                        'usuario' =>
                            $usuario
                    ],
                    [
                        'usuario' => [

                            'required',

                            'string',

                            'min:4',

                            'max:50',

                            'regex:/^[a-z0-9._-]+$/',

                            'unique:usuario,usuario'

                        ]
                    ],
                    [
                        'usuario.regex' =>
                            'El nombre de usuario solo puede contener letras minúsculas, números, punto, guion y guion bajo.'
                    ]
                );


            if (
                $validator->passes()
            ) {

                return $usuario;

            }


            foreach (
                $validator
                    ->errors()
                    ->all()
                as $error
            ) {

                $this->error(
                    $error
                );

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Contraseña
    |--------------------------------------------------------------------------
    */

    private function solicitarPassword(): string
    {

        while (
        true
        ) {

            $password =
                (string)
                $this->secret(
                    'Contraseña'
                );


            $validator =
                Validator::make(
                    [
                        'password' =>
                            $password
                    ],
                    [
                        'password' => [

                            'required',

                            'string',

                            'max:128',

                            Password::min(
                                10
                            )
                                ->letters()
                                ->numbers()
                                ->symbols()

                        ]
                    ]
                );


            if (
                $validator->fails()
            ) {

                foreach (
                    $validator
                        ->errors()
                        ->all()
                    as $error
                ) {

                    $this->error(
                        $error
                    );

                }


                continue;

            }


            $confirmacion =
                (string)
                $this->secret(
                    'Confirme la contraseña'
                );


            if (
                !hash_equals(
                    $password,
                    $confirmacion
                )
            ) {

                $this->error(
                    'Las contraseñas no coinciden.'
                );


                continue;

            }


            return $password;

        }

    }

}
