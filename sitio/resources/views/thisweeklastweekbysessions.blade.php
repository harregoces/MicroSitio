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
        <div id="view-name"></div>
        <div id="active-users-container"></div-->
    </header>
    <div class="Chartjs">
        <!--h3>This Week vs Last Week (by sessions)</h3-->
        <figure class="Chartjs-figure" id="chart-1-container">Loading...</figure>
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

    <!-- Include the ViewSelector2 component script. >
    <script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/date-range-selector.js"></script-->

    <!-- Include the DateRangeSelector component script. >
    <script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/view-selector2.js"></script-->

    <!-- Include the ActiveUsers component script. >
    <script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/active-users.js"></script-->

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

        renderWeekOverWeekChart();

        /**
         * Draw the a chart.js line chart with data from the specified view that
         * overlays session data for the current week over session data for the
         * previous week.
         */
        function renderWeekOverWeekChart() {
            var ids = "ga:{{$task->ga_view}}";
            // Adjust `now` to experiment with different days, for testing only...
            var now = moment(); // .subtract(3, 'day');

            var thisWeek = query({
                'ids': ids,
                'dimensions': 'ga:date,ga:nthDay',
                'metrics': 'ga:sessions',
                'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
                'end-date': moment(now).format('YYYY-MM-DD')
            });

            var lastWeek = query({
                'ids': ids,
                'dimensions': 'ga:date,ga:nthDay',
                'metrics': 'ga:sessions',
                'start-date': moment(now).subtract(1, 'day').day(0).subtract(1, 'week')
                    .format('YYYY-MM-DD'),
                'end-date': moment(now).subtract(1, 'day').day(6).subtract(1, 'week')
                    .format('YYYY-MM-DD')
            });

            Promise.all([thisWeek, lastWeek]).then(function(results) {

                var data1 = results[0].rows.map(function(row) { return +row[2]; });
                var data2 = results[1].rows.map(function(row) { return +row[2]; });
                var labels = results[1].rows.map(function(row) { return +row[0]; });

                labels = labels.map(function(label) {
                    return moment(label, 'YYYYMMDD').format('ddd');
                });

                var data = {
                    labels : labels,
                    datasets : [
                        {
                            label: 'Last Week',
                            fillColor : 'rgba(220,220,220,0.5)',
                            strokeColor : 'rgba(220,220,220,1)',
                            pointColor : 'rgba(220,220,220,1)',
                            pointStrokeColor : '#fff',
                            data : data2
                        },
                        {
                            label: 'This Week',
                            fillColor : 'rgba(151,187,205,0.5)',
                            strokeColor : 'rgba(151,187,205,1)',
                            pointColor : 'rgba(151,187,205,1)',
                            pointStrokeColor : '#fff',
                            data : data1
                        }
                    ]
                };

                new Chart(makeCanvas('chart-1-container')).Line(data);
                generateLegend('legend-1-container', data.datasets);
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
