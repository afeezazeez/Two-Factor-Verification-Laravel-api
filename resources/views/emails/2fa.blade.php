@component('mail::message')
# Hello

Your 2FA code is  <b>{{$token}}</b>

Thanks,<br>
{{ config('app.name') }}
@endcomponent


