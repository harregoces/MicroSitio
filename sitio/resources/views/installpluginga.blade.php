<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{!! asset('public/css/bootstrap_css/bootstrap.min.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('public/plugin/select2/css/select2.min.css') !!}" />
</head>
<body>

<div class="container">

    <form action="installpluginga2" method="post">
        {{ csrf_field() }}
    <fieldset>
        <legend> Por favor proporcione el Tracking ID</legend>
    <div>
        <input type="text" class="form-control" required="true" name="trackingid"/>
    </div>

    <div>
        <button type="submit" class="btn btn-primary form-control btn-sm">Guardar</button>
    </div>

    </fieldset>
    </form>


</div>




<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('public/js/jquery-3.2.1.slim.min.js') }}" ></script>
<script src="{{ asset('public/js/popper.min.js') }}" ></script>
<script src="{{ asset('public/js/bootstrap_js/bootstrap.min.js') }}" ></script>
<script src="{{ asset('public/plugin/select2/js/select2.full.min.js') }}" ></script>

<script>
    $(document).ready( function(){


        $("#dropdown").select2({
            placeholder: 'Por favor, seleccione una opción',
            allowClear: true
        });
    });

    function formatState (state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "/user/pages/images/flags";
        var $state = $(
                '<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    };

</script>

</body>

</html>
