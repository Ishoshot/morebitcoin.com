@component('mail::message')
<h1>
Hi Admin,
</h1>

<p>{{ $user->profile->first_name }} - {{ $user->email }} just initiated an investment on MoreBitcoin.</p>
<p> Please confirm this Investment</p>

@component('mail::panel')
Investor: {{ $user->profile->last_name }} {{ $user->profile->first_name }} - ({{ $user->email }})<br/>
Investment Plan: {{ $investment->plan }}<br/>
Amount Invested (USD): {{ $investment->amountUSD }}<br/>
Amount Invested (BTC): {{ $investment->amountBTC }}<br/>
Investment Ref: {{ $investment->reference }}<br/>
Investment Date: {{ $investment->created_at }}<br/>
@endcomponent

<p>Investment Profit breakdown are as follows:</p>

@component('mail::panel')
<ul>
    <li><p>{{ $data['duration'] }}</p></li>
    <li><p>{{ $data['first'] }}</p></li>
    <li><p>{{ $data['second'] }}</p></li>
    <li><p>{{ $data['third'] }}</p></li>
</ul>
@endcomponent

<p class="text-muted"> Unconfirmed Investments that exceeds 1 hour will be Cancelled </p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
