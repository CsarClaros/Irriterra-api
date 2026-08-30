<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Todos los usuarios autenticados podrán realizar la solicitud.
     *
     * Más adelante esta lógica será controlada mediante Policies y Roles.
     */
    public function authorize(): bool
    {
        return true;
    }
}