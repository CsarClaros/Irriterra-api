<?php

namespace App\Http\Requests\Publico;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;


class EnviarContactoRequest
    extends BaseRequest
{

    /*
    |--------------------------------------------------------------------------
    | Reglas
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {

        return [

            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'correo' => [
                'required',
                'email',
                'max:150',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:30',
            ],

            'motivo' => [
                'required',

                Rule::in([
                    'COTIZACION',
                    'PRODUCTO',
                    'REPUESTOS',
                    'SOPORTE',
                    'OTRO',
                ]),
            ],

            'mensaje' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],

            'turnstile_token' => [
                'required',
                'string',
                'max:2048',
            ],

            'turnstile_token.required' =>
                'Debe completar la verificación de seguridad.',

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Mensajes
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {

        return [

            'nombre.required' =>
                'El nombre es obligatorio.',

            'nombre.min' =>
                'El nombre debe contener al menos 2 caracteres.',

            'nombre.max' =>
                'El nombre no puede superar los 100 caracteres.',


            'correo.required' =>
                'El correo es obligatorio.',

            'correo.email' =>
                'El correo ingresado no es válido.',

            'correo.max' =>
                'El correo no puede superar los 150 caracteres.',


            'telefono.max' =>
                'El teléfono no puede superar los 30 caracteres.',


            'motivo.required' =>
                'Debe seleccionar un motivo de contacto.',

            'motivo.in' =>
                'El motivo seleccionado no es válido.',


            'mensaje.required' =>
                'El mensaje es obligatorio.',

            'mensaje.min' =>
                'El mensaje debe contener al menos 10 caracteres.',

            'mensaje.max' =>
                'El mensaje no puede superar los 2000 caracteres.',

        ];

    }

}
