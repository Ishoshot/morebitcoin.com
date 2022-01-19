@component('mail::message')

A new user has been onboarded on MoreBitcoin.

<h3>User Details</h3>

FIRSTNAME: {{ $user->profile->first_name }} <br>
FIRSTNAME: {{ $user->profile->last_name }} <br>
PHONE NUMBER: {{ $user->profile->phone }} <br>
EMAIL ADDRESS: {{ $user->email}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
