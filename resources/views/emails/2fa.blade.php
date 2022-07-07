@component('mail::message')
# Hello

Your 2FA code is  <b>{{$token}}</b>.

Token expires in 10mins.

Thanks,<br>
{{ config('app.name') }}
@endcomponent


