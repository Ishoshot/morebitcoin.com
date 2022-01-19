@component('mail::message')
<h1>
Hi {{$firstname}},
</h1>

<p>Yay! You Withdrawal has been processed MoreBitcoin and you have been credited. </p>
<p> Please be informed that this might take additional 2-3 mins for funds to reflect in your wallet.</p>

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
