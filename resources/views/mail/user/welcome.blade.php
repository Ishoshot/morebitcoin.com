@component('mail::message')
Dear {{ $firstname }},

<p class="text-justify">
We proudly welcome you as the newest member of MoreBitcoin. We are confident that this step that you have taken will bring you fulfilling returns in the now and in the future.
</p>

<p class="text-justify">
Choosing a reliable company for making financial investments requires intelligent thinking. We are an experienced investment company that offers diverse business options to customers. We trade in monetary, financial and crypto currency in various large scaled markets. Our company aims at achieving great milestones for her customers.
</p>

<div style="background-color: #9e9ea7" class="pad">
<h2>Account Verification Code</h2>
<p>
Use the Code: {{ $code }} to verify your account inorder to start Investing!
</p>
</div>

<p class="text-justify">
Other sites let you buy cryptocurrency. We help you invest in it. MoreBitcoin abstracts the trading experience by making it possible to grow your assets without interfacing directly with exchanges.
</p>

<p class="text-justify">
Investing in cryptocurrency can be intimidating, especially for beginners. Sometimes managing a crypto investment is daunting due to the uncertainty and volatility of the market, as well as the time investment needed to be successful.
</p>


Thanks,<br>
{{ config('app.name') }}
@endcomponent
