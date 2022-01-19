@component('mail::message')
<h1>
Hi {{$firstname}},
</h1>

<p>You investment with MoreBitcoin has been confirmed and is ongoing.</p>
<p>Please be informed that you cannot terminate this investment as you are indulged to be patient during the investment period.</p>

@component('mail::panel')
Investment Plan: {{ $investment->plan }}<br/>
Amount Invested (USD): {{ $investment->amountUSD }}<br/>
Amount Invested (BTC): {{ $investment->amountBTC }}<br/>
Investment Ref: {{ $investment->reference }}<br/>
Investment Date: {{ $investment->updated_at }}<br/>
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


Thanks,<br>
{{ config('app.name') }}
@endcomponent
