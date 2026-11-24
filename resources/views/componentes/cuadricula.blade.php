@php
    $dias = config('escolar.dias', [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes']);
    $datos = $datos ?? [];
@endphp

<div class="tabla-contenedor">
    <table class="cuadricula">
        <thead>
            <tr>
                <th class="cuadricula__hora">Hora</th>
                @foreach ($dias as $nombreDia)
                    <th>{{ $nombreDia }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach ($franjas as $franja)
            @if ($franja->es_receso)
                <tr>
                    <th class="cuadricula__hora">{{ substr($franja->hora_inicio, 0, 5) }}–{{ substr($franja->hora_fin, 0, 5) }}</th>
                    <td class="cuadricula__receso" colspan="{{ count($dias) }}">Receso</td>
                </tr>
            @else
                <tr>
                    <th class="cuadricula__hora">{{ substr($franja->hora_inicio, 0, 5) }}–{{ substr($franja->hora_fin, 0, 5) }}</th>
                    @foreach ($dias as $dia => $nombreDia)
                        <td class="cuadricula__celda">
                            @include($celda, ['dia' => $dia, 'franja' => $franja] + $datos)
                        </td>
                    @endforeach
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>