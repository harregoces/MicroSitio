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

    <div class="Chartjs">
        <h3>Top Browsers (by pageview)</h3>
        <figure class="Chartjs-figure" id="chart-3-container"></figure>
        <ol class="Chartjs-legend" id="legend-3-container"></ol>
    </div>

    <!-- This demo uses the Chart.js graphing library and Moment.js to do date
     formatting and manipulation. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>

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

        renderTopBrowsersChart();

        /**
         * Draw the a chart.js doughnut chart with data from the specified view that
         * show the top 5 browsers over the past seven days.
         */
        function renderTopBrowsersChart() {

            var ids = "ga:{{$task->ga_view}}";

            query({
                'ids': ids,
                'dimensions': 'ga:browser',
                'metrics': 'ga:pageviews',
                'sort': '-ga:pageviews',
                'max-results': 5
            })
                .then(function(response) {

                    var data = [];
                    var colors = ['#4D5360','#949FB1','#D4CCC5','#E2EAE9','#F7464A'];

                    response.rows.forEach(function(row, i) {
                        data.push({ value: +row[1], color: colors[i], label: row[0] });
                    });

                    new Chart(makeCanvas('chart-3-container')).Doughnut(data);
                    generateLegend('legend-3-container', data);
                });
        }

        /**
         * Extend the Embed APIs `gapi.analytics.report.Data` component to
         * return a promise the is fulfilled with the value returned by the API.
         * @param {Object} params The request parameters.
         * @return {Promise} A promise.
         */
        function query(params) {
            return new Promise(function(resolve, reject) {
                var data = new gapi.analytics.report.Data({query: params});
                data.once('success', function(response) { resolve(response); })
                    .once('error', function(response) { reject(response); })
                    .execute();
            });
        }


        /**
         * Create a new canvas inside the specified element. Set it to be the width
         * and height of its container.
         * @param {string} id The id attribute of the element to host the canvas.
         * @return {RenderingContext} The 2D canvas context.
         */
        function makeCanvas(id) {
            var container = document.getElementById(id);
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');

            container.innerHTML = '';
            canvas.width = container.offsetWidth;
            canvas.height = container.offsetHeight;
            container.appendChild(canvas);

            return ctx;
        }


        /**
         * Create a visual legend inside the specified element based off of a
         * Chart.js dataset.
         * @param {string} id The id attribute of the element to host the legend.
         * @param {Array.<Object>} items A list of labels and colors for the legend.
         */
        function generateLegend(id, items) {
            var legend = document.getElementById(id);
            legend.innerHTML = items.map(function(item) {
                var color = item.color || item.fillColor;
                var label = item.label;
                return '<li><i style="background:' + color + '"></i>' +
                    escapeHtml(label) + '</li>';
            }).join('');
        }


        // Set some global Chart.js defaults.
        Chart.defaults.global.animationSteps = 60;
        Chart.defaults.global.animationEasing = 'easeInOutQuart';
        Chart.defaults.global.responsive = true;
        Chart.defaults.global.maintainAspectRatio = false;


        /**
         * Escapes a potentially unsafe HTML string.
         * @param {string} str An string that may contain HTML entities.
         * @return {string} The HTML-escaped string.
         */
        function escapeHtml(str) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }

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
