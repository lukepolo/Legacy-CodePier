<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:700|Roboto:300,500" rel="stylesheet">
</head>
<body>
    <style>
        @media only screen and (max-width: 600px) {
            .content {
                width: 100% !important;
                padding: 0px;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td></td>
            <td class="container">
                <div class="content">
                    {{ $header or '' }}

                    {{ Illuminate\Mail\Markdown::parse($slot) }}

                    {{ $subcopy or '' }}

                    {{ $footer or '' }}
                </div>
            </td>
            <td></td>
        </tr>
    </table>
</body>
</html>
