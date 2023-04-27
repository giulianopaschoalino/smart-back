@component('mail::message')
# Recuperação de senha

O seu código de recuperação de senha é <h4>{{$pin}}</h4>
<p>Por favor, não compartilhe o código com ninguém.</p>

Agradecido,<br>
{{ config('app.name') }}
@endcomponent
