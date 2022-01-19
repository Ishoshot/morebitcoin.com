@component('mail::message')
<h1>
Hi {{$firstname}},
</h1>

<p>You have initiated a withdrawal on MoreBitcoin. </p>
<p> Please be informed that this is pending and might take up to 15 mins for confirmation and processing. </p>

@component('mail::panel')
Withdrawal ID: {{ $withdrawal->uid }}<br/>
Amount (USD): {{ $withdrawal->amountUSD }}<br/>
Amount (BTC): {{ $withdrawal->amountBTC }}<br/>
Wallet: {{ $withdrawal->wallet }}<br/>
Date: {{ $withdrawal->created_at }}<br/>
@endcomponent


<p class="text-muted"> Contact Our Support Team if you are having any difficulty or issues with this withdrawal.</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
