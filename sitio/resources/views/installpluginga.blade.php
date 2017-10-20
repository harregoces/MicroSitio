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

    <form action="/installpluginga2" method="post">
        {{ csrf_field() }}
        <fieldset>

            <legend> Seleccione un Cuenta, propietario y vista</legend>

            <div>

                Cuenta
                <select required="" class="js-example-basic-single js-states form-control" name="account" id="account">
                    <option value ="">Por favor, seleccione una opción</option>
                    @foreach ($listAccount as $account)
                        <option value="{{$account['id']}}">{{$account['name']}}</option>
                    @endforeach
                </select>

                Propiedad
                <select required="" class="js-example-basic-single js-states form-control" name="property" id="property">
                    <option value ="">Por favor, seleccione una cuenta primero</option>
                </select>

                Vista
                <select required="" class="js-example-basic-single js-states form-control" name="view" id="view">
                    <option value ="">Por favor, seleccione una propiedad primero</option>
                </select>

                <input type="hidden" name="returnurl" value="{{$returnurl}}">
                <input type="hidden" name="state" value="{{$state}}">

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


        $("#account").select2({
            placeholder: 'Por favor, seleccione una opción',
            allowClear: true
        });

        $("#property").select2({
            placeholder: 'Por favor, seleccione una opción',
            allowClear: true
        });

        $("#view").select2({
            placeholder: 'Por favor, seleccione una opción',
            allowClear: true
        });


        $("#account").on('change',function(){

            var account = $(this).val();

            $('#property').empty();
            $('#view').empty();

            $('#property').select2({
                allowClear: true,
                ajax: {
                    type: 'GET',
                    url: '/getProperty/account/' + account + '/state/' + '{{$state}}',
                    processResults: function (data) {
                        var data = $.map(data, function (obj) {
                            obj.id = obj.id;
                            obj.text = obj.name;
                            return obj;
                        });
                        return {
                            results: data
                        };
                    }
                }
            });

            $('#property').on('change',function(){
                var property = $(this).val();
                var account = $("#account").val();
                $('#view').empty();

                $('#view').select2({
                    allowClear: true,
                    ajax: {
                        type: 'GET',
                        url: '/getView/account/' + account+'/property/'+property,
                        processResults: function (data) {
                            var data = $.map(data, function (obj) {
                                obj.id = obj.id;
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



    });

</script>

</body>

</html>
