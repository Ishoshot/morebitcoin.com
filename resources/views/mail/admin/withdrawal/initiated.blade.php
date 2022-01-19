@component('mail::message')
<h1>
Hi Admin,
</h1>

<p>{{ $user->profile->first_name }} - {{ $user->email }} just initiated a withdrawal request on MoreBitcoin.</p>
<p> Please Process this Withdrawal, and Kindly Update the status {{ env("CLIENT_APP_URL").'admin/withdrawal/'.$withdrawal->id }}</p>

@component('mail::panel')
Investor: {{ $user->profile->last_name }} {{ $user->profile->first_name }} - ({{ $user->email }})<br/>
Withdrawal ID: {{ $withdrawal->uid }}<br/>
Amount (USD): {{ $withdrawal->amountUSD }}<br/>
Amount (BTC): {{ $withdrawal->amountBTC }}<br/>
Wallet: {{ $withdrawal->wallet }}<br/>
Date: {{ $withdrawal->created_at }}<br/>
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
