@component('mail::message')
<h1>
Hi {{$firstname}},
</h1>

<p>We're glad to inform you that your investment on MoreBitcoin has matured.</p>

@component('mail::panel')
Investment Plan: {{ $investment->plan }}<br/>
Amount Invested (USD): {{ $investment->amountUSD }}<br/>
Amount Invested (BTC): {{ $investment->amountBTC }}<br/>
Investment Ref: {{ $investment->reference }}<br/>
Investment Date: {{ $investment->created_at }}<br/>
@endcomponent

<p>Your Investment Profit breakdown are as follows:</p>

@component('mail::panel')
<ul>
    <li><p>{{ $data['duration'] }}</p></li>
    <li><p>{{ $data['first'] }}</p></li>
    <li><p>{{ $data['second'] }}</p></li>
    <li><p>{{ $data['third'] }}</p></li>
</ul>
@endcomponent

<p class="text-muted"> If there are any issues regarding this investment, Please do well to reach out to our Support Team.</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
