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

    <form action="/installplugingtm3" method="post">
        {{ csrf_field() }}
    <fieldset>

        <legend> Seleccione un Contenedor de Google Tag Manager </legend>

    <div>

        <select required="" class="js-example-basic-single js-states form-control" name="gtmaccount" id="dropdown">
            <option value ="">Por favor, seleccione una opción</option>
            @foreach ($accounts as $account)
                <optgroup label="{{$account['account']}}">
                    @foreach ($account['containers'] as $container)
                        <option value="{{$container['publicId']}}">{{$container['containerName']}} ({{$container['publicId']}})</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>


        <select required="" class="js-example-basic-single js-states form-control" name="workspace" id="worspaceDropdown">
            <option value ="">Por favor, seleccione una cuenta primero</option>
        </select>

    </div>

    <div>
        <button type="submit" class="btn btn-primary form-control btn-sm">Guardar</button>
    </div>

    </fieldset>
    </form>


</div>




<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('public/js/jquery-3.2.1.min.js') }}" ></script>
<script src="{{ asset('public/js/popper.min.js') }}" ></script>
<script src="{{ asset('public/js/bootstrap_js/bootstrap.min.js') }}" ></script>
<script src="{{ asset('public/plugin/select2/js/select2.full.min.js') }}" ></script>

<script>
    $(document).ready( function(){


        $("#dropdown").select2({
            placeholder: 'Por favor, seleccione una opción',
            allowClear: true
        });

        $('#worspaceDropdown').select2({
            placeholder: 'Por favor, seleccione una opción',
            allowClear: true
        });

        $("#dropdown").on('change',function(){

            var gtmccount = $(this).val();

            $('#worspaceDropdown').empty();

            $('#worspaceDropdown').select2({
                allowClear: true,
                ajax: {
                    type: 'GET',
                    url: '/getWorspace/gtmaccount/' + gtmccount,
                    processResults: function (data) {
                        var data = $.map(data, function (obj) {
                            obj.id = obj.path;
                            obj.text = obj.name;
                            return obj;
                        });

                        return {
                            results : data
                        };
                    }
                }
            });

        });
    });

</script>

</body>

</html>
