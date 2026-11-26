{{-- Logo y nombre del sistema con el nombre de la escuela (sale de config/escolar.php) --}}
<div class="marca">
    <span class="marca__logo">SE</span>
    <span>
        Sistema escolar
        <small class="marca__escuela">{{ config('escolar.escuela.nombre') }} · {{ config('escolar.escuela.turno') }}</small>
    </span>
</div>
