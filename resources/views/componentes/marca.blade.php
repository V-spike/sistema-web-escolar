{{-- Logo y nombre de la escuela (los datos salen de config/escolar.php) --}}
<div class="marca">
    <img class="marca__logo" src="{{ asset('img/logo-escuela.png') }}" alt="Logo de la escuela">
    <span>
        Sistema escolar
        <small class="marca__escuela">{{ config('escolar.escuela.nombre') }} · {{ config('escolar.escuela.turno') }}</small>
    </span>
</div>
