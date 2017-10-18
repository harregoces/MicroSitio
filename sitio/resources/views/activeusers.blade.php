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


    <script>
        (function(w,d,s,g,js,fs){
            g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
            js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
            js.src='https://apis.google.com/js/platform.js';
            fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
        }(window,document,'script'));
    </script>

    <header>
        <!--div id="embed-api-auth-container"></div>
        <div id="view-selector-container"></div>
        <div id="view-name"></div-->
        <div id="active-users-container"></div>
    </header>
    <div class="Chartjs">
        <!--h3>This Week vs Last Week (by sessions)</h3-->
        <figure class="Chartjs-figure" id="chart-1-container"></figure>
        <ol class="Chartjs-legend" id="legend-1-container"></ol>
    </div>
    <!--div class="Chartjs">
        <h3>This Year vs Last Year (by users)</h3>
        <figure class="Chartjs-figure" id="chart-2-container"></figure>
        <ol class="Chartjs-legend" id="legend-2-container"></ol>
    </div>
    <div class="Chartjs">
        <h3>Top Browsers (by pageview)</h3>
        <figure class="Chartjs-figure" id="chart-3-container"></figure>
        <ol class="Chartjs-legend" id="legend-3-container"></ol>
    </div>
    <div class="Chartjs">
        <h3>Top Countries (by sessions)</h3>
        <figure class="Chartjs-figure" id="chart-4-container"></figure>
        <ol class="Chartjs-legend" id="legend-4-container"></ol>
    </div-->

    <!-- This demo uses the Chart.js graphing library and Moment.js to do date
     formatting and manipulation. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>

    <!-- Include the ViewSelector2 component script. -->
    <script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/date-range-selector.js"></script>

    <!-- Include the DateRangeSelector component script. -->
    <script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/view-selector2.js"></script>

    <!-- Include the ActiveUsers component script. -->
    <script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/active-users.js"></script>

    <!-- Include the CSS that styles the charts. -->
    <link rel="stylesheet" href="https://ga-dev-tools.appspot.com/public/css/chartjs-visualizations.css">

<script>

    gapi.analytics.ready(function() {

        /**
         * Authorize the user immediately if the user has already granted access.
         * If no access has been created, render an authorize button inside the
         * element with the ID "embed-api-auth-container".
         */
        gapi.analytics.auth.authorize({
            'serverAuth': {
                'access_token': "{{$token}}"
            }
        });



        /**
         * Create a new ActiveUsers instance to be rendered inside of an
         * element with the id "active-users-container" and poll for changes every
         * five seconds.
         */
        var activeUsers = new gapi.analytics.ext.ActiveUsers({
            container: 'active-users-container',
            pollingInterval: 5
        });


        /**
         * Add CSS animation to visually show the when users come and go.
         */
        activeUsers.once('success', function() {

            var element = this.container.firstChild;
            var timeout;

            this.on('change', function(data) {
                var element = this.container.firstChild;
                var animationClass = data.delta > 0 ? 'is-increasing' : 'is-decreasing';
                element.className += (' ' + animationClass);

                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    element.className =
                        element.className.replace(/ is-(increasing|decreasing)/g, '');
                }, 3000);
            });
        });

        activeUsers.set({'ids': "ga:{{$task->ga_view}}"}).execute();

    });
</script>

</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('public/js/jquery-3.2.1.slim.min.js') }}" ></script>
<script src="{{ asset('public/js/popper.min.js') }}" ></script>
<script src="{{ asset('public/js/bootstrap_js/bootstrap.min.js') }}" ></script>

</body>

</html>
