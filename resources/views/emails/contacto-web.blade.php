<!doctype html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Nueva consulta web
    </title>

</head>

<body
    style="
        margin: 0;
        padding: 0;
        background: #f5f5f5;
        font-family: Arial, Helvetica, sans-serif;
        color: #222222;
    "
>

<div
    style="
            max-width: 680px;
            margin: 0 auto;
            padding: 32px 16px;
        "
>

    <div
        style="
                background: #ffffff;
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
            "
    >

        <!-- Encabezado -->

        <div
            style="
                    padding: 24px 28px;
                    background: #ffffff;
                    border-bottom: 1px solid #e5e7eb;
                "
        >

            <h1
                style="
                        margin: 0;
                        font-size: 22px;
                    "
            >
                Nueva consulta desde Irriterra
            </h1>

            <p
                style="
                        margin: 8px 0 0;
                        color: #666666;
                    "
            >
                {{ $motivoTexto }}
            </p>

        </div>


        <!-- Datos -->

        <div
            style="
                    padding: 28px;
                "
        >

            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="
                        border-collapse: collapse;
                    "
            >

                <tr>

                    <td
                        style="
                                padding: 8px 0;
                                width: 140px;
                                color: #666666;
                                vertical-align: top;
                            "
                    >
                        Nombre
                    </td>

                    <td
                        style="
                                padding: 8px 0;
                            "
                    >
                        {{ $datos['nombre'] }}
                    </td>

                </tr>


                <tr>

                    <td
                        style="
                                padding: 8px 0;
                                color: #666666;
                                vertical-align: top;
                            "
                    >
                        Correo
                    </td>

                    <td
                        style="
                                padding: 8px 0;
                            "
                    >
                        <a
                            href="mailto:{{ $datos['correo'] }}"
                        >
                            {{ $datos['correo'] }}
                        </a>
                    </td>

                </tr>


                <tr>

                    <td
                        style="
                                padding: 8px 0;
                                color: #666666;
                                vertical-align: top;
                            "
                    >
                        Teléfono
                    </td>

                    <td
                        style="
                                padding: 8px 0;
                            "
                    >
                        {{ $datos['telefono'] ?: 'No proporcionado' }}
                    </td>

                </tr>


                <tr>

                    <td
                        style="
                                padding: 8px 0;
                                color: #666666;
                                vertical-align: top;
                            "
                    >
                        Motivo
                    </td>

                    <td
                        style="
                                padding: 8px 0;
                            "
                    >
                        {{ $motivoTexto }}
                    </td>

                </tr>

            </table>


            <div
                style="
                        margin-top: 24px;
                    "
            >

                <p
                    style="
                            margin: 0 0 10px;
                            color: #666666;
                        "
                >
                    Mensaje
                </p>

                <div
                    style="
                            padding: 18px;
                            border-radius: 8px;
                            background: #f7f7f7;
                            white-space: pre-wrap;
                            line-height: 1.6;
                        "
                >{{ $datos['mensaje'] }}</div>

            </div>

        </div>


        <!-- Footer -->

        <div
            style="
                    padding: 18px 28px;
                    border-top: 1px solid #e5e7eb;
                    color: #777777;
                    font-size: 12px;
                "
        >

            Mensaje recibido desde el formulario web de
            Irriterra S.R.L.

        </div>

    </div>

</div>

</body>

</html>
