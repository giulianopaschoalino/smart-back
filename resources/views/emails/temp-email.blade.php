@component('mail::message')
## Olá {{ $name }}!

Devido à uma atualização do sistema por questão de segurança, foi necessário atualizar sua senha.

@if ($has_password)
Para acessar o sistema, use seu login (seu e-mail de cadastro) e use a nova senha: <strong style="text-decoration: underline">{{ $password }}</strong>, ou clique em "Esqueceu a senha?" e redefina sua senha.
@else
Para acessar o sistema, por favor use o fluxo de "Esqueceu a senha?" pelo Web e redefina sua senha.
@endif

Pedimos desculpas pelo inconveniente!

### Atenciosamente, Equipe {{ config('app.name') }}
@endcomponent
