{{-- Una celda de la cuadrícula de disponibilidad (T-50).
     La incluye componentes.cuadricula de Gloria y recibe $dia, $franja y $marcadas.
     El guion bajo al inicio del nombre indica que es una vista parcial: no se abre sola. --}}
@php
    // El valor que se envía: día, guion e id de la franja. Ejemplo: "2-5" = martes, franja 5
    $valor = $dia . '-' . $franja->id;
    $nombreDia = config('escolar.dias')[$dia] ?? 'Día ' . $dia;
    $hora = substr($franja->hora_inicio, 0, 5) . ' a ' . substr($franja->hora_fin, 0, 5);
@endphp

<label class="disponibilidad__casilla">
    <input type="checkbox"
           name="espacios[]"
           value="{{ $valor }}"
           @checked(in_array($valor, $marcadas))
           aria-label="{{ $nombreDia }}, de {{ $hora }}">
</label>
