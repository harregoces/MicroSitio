<!doctype html>
<html lang="{{ app()->getLocale() }}">

<h1>Sessions PIE</h1>
<iframe src="/dashboard/merchantid/{{$idcliente}}/type/sessionspie" width="800px" height="200px"></iframe>

<h1>Sessions Per Country PIE Chart</h1>
<iframe src="/dashboard/merchantid/{{$idcliente}}/type/sessionspercountrypie" width="800px" height="300px"></iframe>

<h1>Sessions Per Browser</h1>
<iframe src="/dashboard/merchantid/{{$idcliente}}/type/sessionsperbrowser" width="800px" height="300px"></iframe>

<h1>This Week Vs Last Week By Session</h1>
<iframe src="/dashboard/merchantid/{{$idcliente}}/type/thisweeklastweekbysessions" width="800px" height="300px"></iframe>

<h1>Active Users</h1>
<iframe src="/dashboard/merchantid/{{$idcliente}}/type/activeusers" width="800px" height="40px"></iframe>

<h1>This Year Vs Last Year By Users</h1>
<iframe src="/dashboard/merchantid/{{$idcliente}}/type/thisyearlastyearbyusers" width="800px" height="300px"></iframe>

<h1>Top Browsers (by pageview)</h1>
<iframe src="/dashboard/merchantid/{{$idcliente}}/type/topbrowsersbypageviewpie" width="800px" height="300px"></iframe>

<h1>Top Countries By Session PIE</h1>
<iframe src="/dashboard/merchantid/{{$idcliente}}/type/topcountriesbysessionspie" width="800px" height="300px"></iframe>

</html>