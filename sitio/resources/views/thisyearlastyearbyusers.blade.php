<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{!! asset('public/css/bootstrap_css/bootstrap.min.css') !!}" />

    <style>
        /*
        .loading{position:relative;min-height:240px;min-width:90px;overflow:hidden;pointer-events:none}.loading>*{display:none!important;visibility:hidden!important}.loading:after,.loading:before{content:''!important;position:absolute;top:50%;left:50%;display:block;border:10px solid #337ab7;border-radius:50%}.loading:before{width:80px;height:80px;margin-top:-40px;margin-left:-40px;border-right-color:transparent;border-left-color:transparent;-webkit-animation:rotation 3s linear infinite;-o-animation:rotation 3s linear infinite;animation:rotation 3s linear infinite}.loading:after{width:40px;height:40px;margin-top:-20px;margin-left:-20px;border-top-color:transparent;border-bottom-color:transparent;-webkit-animation:rotation 1s linear infinite;-o-animation:rotation 1s linear infinite;animation:rotation 1s linear infinite}                                                                                                                                                            }.loading: after {
        */
    </style>
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
        <h3>This Year vs Last Year (by users)</h3>
        <figure class="Chartjs-figure" id="chart-2-container">Loading...</figure>
        <ol class="Chartjs-legend" id="legend-2-container"></ol>
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

        renderYearOverYearChart();

        /**
         * Draw the a chart.js bar chart with data from the specified view that
         * overlays session data for the current year over session data for the
         * previous year, grouped by month.
         */
        function renderYearOverYearChart() {

            var ids = "ga:{{$task->ga_view}}";

            // Adjust `now` to experiment with different days, for testing only...
            var now = moment(); // .subtract(3, 'day');

            var thisYear = query({
                'ids': ids,
                'dimensions': 'ga:month,ga:nthMonth',
                'metrics': 'ga:users',
                'start-date': moment(now).date(1).month(0).format('YYYY-MM-DD'),
                'end-date': moment(now).format('YYYY-MM-DD')
            });

            var lastYear = query({
                'ids': ids,
                'dimensions': 'ga:month,ga:nthMonth',
                'metrics': 'ga:users',
                'start-date': moment(now).subtract(1, 'year').date(1).month(0)
                    .format('YYYY-MM-DD'),
                'end-date': moment(now).date(1).month(0).subtract(1, 'day')
                    .format('YYYY-MM-DD')
            });

            Promise.all([thisYear, lastYear]).then(function(results) {
                var data1 = results[0].rows.map(function(row) { return +row[2]; });
                var data2 = results[1].rows.map(function(row) { return +row[2]; });
                var labels = ['Jan','Feb','Mar','Apr','May','Jun',
                    'Jul','Aug','Sep','Oct','Nov','Dec'];

                // Ensure the data arrays are at least as long as the labels array.
                // Chart.js bar charts don't (yet) accept sparse datasets.
                for (var i = 0, len = labels.length; i < len; i++) {
                    if (data1[i] === undefined) data1[i] = null;
                    if (data2[i] === undefined) data2[i] = null;
                }

                var data = {
                    labels : labels,
                    datasets : [
                        {
                            label: 'Last Year',
                            fillColor : 'rgba(220,220,220,0.5)',
                            strokeColor : 'rgba(220,220,220,1)',
                            data : data2
                        },
                        {
                            label: 'This Year',
                            fillColor : 'rgba(151,187,205,0.5)',
                            strokeColor : 'rgba(151,187,205,1)',
                            data : data1
                        }
                    ]
                };

                new Chart(makeCanvas('chart-2-container')).Bar(data);
                generateLegend('legend-2-container', data.datasets);
            })
                .catch(function(err) {
                    console.error(err.stack);
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
