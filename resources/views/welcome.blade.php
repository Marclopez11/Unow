<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>REST API Demo</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
            #mydiv {
                font-size: 28px;
                position:fixed;
                top: 60%;
                left: 50%;
                width:30em;
                height:18em;
                margin-top: -9em; /*set to a negative number 1/2 of your height*/
                margin-left: -15em; /*set to a negative number 1/2 of your width*/
            }


            #cambiarColor {
                font-size: 50px;
                font-weight: bold;
            }
        </style>
    </head>
    <body class="antialiased">
        <div id="mydiv">
            <center><b id="cambiarColor">Bienvenido a REST API demo</b></center>
            <center><b>By: Marc López</b></center>
        </div>
    </body>


    <script>
        // Generar un color aleatorio en formato hexadecimal
        function getRandomColor() {
          const letters = '0123456789ABCDEF';
          let color = '#';
          for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
          }
          return color;
        }

        // Cambiar el color del texto cada 1s
        function cambiarColorConstantemente() {
          const elemento = document.getElementById('cambiarColor');
          elemento.style.color = getRandomColor();
        }

        // Cambiar el color (1 segundo)
        setInterval(cambiarColorConstantemente, 1000);
      </script>
</html>
