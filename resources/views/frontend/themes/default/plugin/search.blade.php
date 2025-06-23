<!DOCTYPE html>
<html>
<head>
    <title>Tyre Search Plugin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; background: #f9f9f9; padding: 10px; }
        .form-wrap { background: white; padding: 20px; border-radius: 10px; max-width: 400px; margin: auto; }
        .form-control { width: 100%; padding: 10px; margin-bottom: 10px; }
        .btn { background: #222; color: white; padding: 10px 20px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="form-wrap">
    <form id="pluginSearchForm" method="GET" action="{{ route('plugin.search.submit') }}" target="_blank">
        <input type="hidden" name="client_id" value="{{ request('client_id') }}">
        <input type="hidden" name="token" value="{{ request('token') }}">

        <input type="text" name="vrm" placeholder="Enter Vehicle Reg" class="form-control" required>

        <label>
            <input type="radio" name="fitting_type" value="fully_fitted" checked> Fully Fitted
        </label>
        <label>
            <input type="radio" name="fitting_type" value="mobile"> Mobile Fitted
        </label>

        <button type="submit" class="btn">Search Tyres</button>
    </form>
</div>

</body>
</html>
