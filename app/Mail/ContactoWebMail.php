<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class ContactoWebMail
    extends Mailable
{

    use Queueable;
    use SerializesModels;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(

        public readonly array $datos

    )
    {
    }


    /*
    |--------------------------------------------------------------------------
    | Sobre
    |--------------------------------------------------------------------------
    */

    public function envelope():
    Envelope
    {

        return new Envelope(

            replyTo: [

                new Address(

                    $this
                        ->datos['correo'],

                    $this
                        ->datos['nombre']

                )

            ],

            subject: '[Web - '
            . $this->motivoTexto()
            . '] '
            . $this->datos['nombre']

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Contenido
    |--------------------------------------------------------------------------
    */

    public function content():
    Content
    {

        return new Content(

            view: 'emails.contacto-web',

            with: [

                'datos' =>
                    $this->datos,

                'motivoTexto' =>
                    $this->motivoTexto(),

            ]

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Adjuntos
    |--------------------------------------------------------------------------
    */

    public function attachments():
    array
    {

        return [];

    }


    /*
    |--------------------------------------------------------------------------
    | Motivo legible
    |--------------------------------------------------------------------------
    */

    private function motivoTexto():
    string
    {

        return match (
        $this->datos['motivo']
        ) {

            'COTIZACION' =>
            'Cotización',

            'PRODUCTO' =>
            'Consulta de producto',

            'REPUESTOS' =>
            'Repuestos',

            'SOPORTE' =>
            'Soporte',

            default =>
            'Otra consulta',

        };

    }

}
