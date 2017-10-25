<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{!! asset('public/css/bootstrap_css/bootstrap.min.css') !!}" />
</head>
<body>

<div class="container">

    <div class="row">

        <div class="col">
            <div class="card" style="width: 20rem;">
                <div class="card-body">
                    <h4 class="card-title">Google Tag Manager</h4>
                    <h6 class="card-subtitle mb-2 text-muted">GTM</h6>
                    <p class="card-text">ofrece soluciones de gestión de etiquetas sencillas pero potentes para ayudar a las pequeñas empresas ya las grandes empresas a lanzar programas con mayor rapidez.</p>
                    <a href="https://www.google.com/analytics/tag-manager/" class="card-link">GTM info link</a>
                    @if(isset($task) && $task->gtm_code != null)
                        <p class="alert-info">!El plugin ya esta instalado</p>
                    @else
                        <a href="/installplugingtm/merchantid/{{$idcliente}}/?returnurl=http%3A%2F%2Fmicrositio.com%2Fmerchantid%2F{{$idcliente}}%2F%3Freturned" class="card-link">Instalar</a>
                    @endif

                </div>
            </div>
        </div>


        <div class="col">
            <div class="card" style="width: 20rem;">
                <div class="card-body">
                    <h4 class="card-title">Google Analitics</h4>
                    <h6 class="card-subtitle mb-2 text-muted">GA</h6>
                    <p class="card-text">Es un servicios de análisis web freemium ofrecido por Google que rastrea e informa del tráfico del sitio web. </p>
                    <a href="https://www.google.com/analytics/" class="card-link">GA info link</a>
                    @if (!isset($task) || ( isset($task) && $task->gtm_code == null) )
                        <p class="alert-danger">!Debe instalar primero GTM</p>
                    @elseif(isset($task) && $task->ga_code != null)
                        <p class="alert-info">!El plugin ya esta instalado</p>
                    @else
                        <a href="/installpluginga/merchantid/{{$idcliente}}/?returnurl=http%3A%2F%2Fmicrositio.com%2Fmerchantid%2F{{$idcliente}}%2F%3Freturned" class="card-link">Instalar</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>




<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('public/js/jquery-3.2.1.slim.min.js') }}" ></script>
<script src="{{ asset('public/js/popper.min.js') }}" ></script>
<script src="{{ asset('public/js/bootstrap_js/bootstrap.min.js') }}" ></script>

</body>

</html>
