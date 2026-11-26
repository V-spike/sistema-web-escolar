{{-- Firmas de subdirección y dirección (los nombres salen de config/escolar.php / .env) --}}
<div class="firmas">
    <div class="firmas__una">
        <span class="firmas__nombre">{{ mb_strtoupper(config('escolar.escuela.subdirector')) }}</span>
        <span class="firmas__cargo">Nombre y firma del titular de la subdirección del plantel</span>
    </div>
    <div class="firmas__una">
        <span class="firmas__nombre">{{ mb_strtoupper(config('escolar.escuela.director')) }}</span>
        <span class="firmas__cargo">Nombre y firma del titular de la dirección del plantel</span>
    </div>
</div>
