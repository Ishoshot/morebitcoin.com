@component('mail::message')
<h1>
Hi Admin,
</h1>

<p>You have confimed the investment made by {{ $user->profile->first_name }} - {{ $user->email }} on MoreBitcoin.</p>

@component('mail::panel')
Investor: {{ $user->profile->last_name }} {{ $user->profile->first_name }} - ({{ $user->email }})<br/>
Investment Plan: {{ $investment->plan }}<br/>
Amount Invested (USD): {{ $investment->amountUSD }}<br/>
Amount Invested (BTC): {{ $investment->amountBTC }}<br/>
Investment Ref: {{ $investment->reference }}<br/>
Investment Date: {{ $investment->updated_at }}<br/>
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

Thanks,<br>
{{ config('app.name') }}
@endcomponent
