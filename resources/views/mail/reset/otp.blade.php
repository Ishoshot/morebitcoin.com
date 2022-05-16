@component('mail::message')

You initiated a password reset process on MoreBitcoin. Use the OTP below to complete the process on our website


Code: {{ $otp }} <br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
