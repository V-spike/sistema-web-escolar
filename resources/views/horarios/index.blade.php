@extends('layouts.app')

@section('titulo', 'Acomodar horario')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
@php
    $nombreDe = fn ($x) => match ($vista) {
        'docente' => $x->nombre_completo,
        'aula' => $x->clave . ' · ' . $x->edificio,
        default => $x->nombre,
    };
@endphp

<div class="barra-catalogo">
    <div>
        <h1>Acomodar horario</h1>
        <p class="tenue" style="margin:0">Semestre {{ $semestre->nombre }} · jornada {{ $jornada->hora_entrada_corta }}–{{ $jornada->hora_salida_corta }} · hora clase de {{ $jornada->minutos_por_hora }} min</p>
    </div>
    <div class="barra-catalogo__acciones no-imprimir">
        <div class="pestanas" role="tablist">
            @foreach (['grupo' => 'Por grupo', 'docente' => 'Por docente', 'aula' => 'Por aula'] as $clave => $texto)
                <a href="{{ route('horarios.index', ['vista' => $clave]) }}" @class(['activa' => $vista === $clave])>{{ $texto }}</a>
            @endforeach
        </div>
        <button type="button" class="btn btn--secundario btn--chico" onclick="window.print()">Imprimir</button>
    </div>
</div>

@include('horarios._revision')

@if ($problemas->isNotEmpty())
    <div class="alerta alerta--empalme no-imprimir">
        <h3>Hay {{ $problemas->count() }} {{ $problemas->count() === 1 ? 'clase' : 'clases' }} con empalme</h3>
        <p style="margin:.2rem 0 .4rem">Algo cambió después de acomodarlas (por ejemplo, una disponibilidad o un receso). Ábrelas para corregirlas:</p>
        <ul>
            @foreach ($problemas as $p)
                <li>
                    <a href="{{ route('horarios.edit', $p['clase']) }}">{{ $p['clase']->grupo->nombre }} · {{ $p['clase']->materia->nombre }} · {{ config('escolar.dias')[$p['clase']->dia] }} {{ $p['clase']->rango }}</a>:
                    {{ collect($p['revision']->empalmes)->pluck('causa')->join(' ') }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if ($lista->isEmpty())
    <div class="panel">
        <p style="margin:0">Todavía no hay {{ ['grupo' => 'grupos', 'docente' => 'docentes', 'aula' => 'aulas'][$vista] }} activos para este semestre.</p>
    </div>
@else
    <form method="GET" action="{{ route('horarios.index') }}" class="selector no-imprimir">
        <input type="hidden" name="vista" value="{{ $vista }}">
        <label for="id" class="tenue">{{ ['grupo' => 'Grupo', 'docente' => 'Docente', 'aula' => 'Aula'][$vista] }}</label>
        <select id="id" name="id" class="campo" onchange="this.form.submit()">
            @foreach ($lista as $item)
                <option value="{{ $item->id }}" @selected($elegido && $item->id === $elegido->id)>{{ $nombreDe($item) }}</option>
            @endforeach
        </select>
        <noscript><button class="btn btn--secundario btn--chico">Ver</button></noscript>
    </form>

    <h2 class="solo-imprimir">Horario de {{ $nombreDe($elegido) }} · {{ $semestre->nombre }}</h2>

    <div @class(['rejilla-2' => $vista === 'grupo'])>
        <div class="panel">
            <div class="panel__titulo">
                <h2>{{ $nombreDe($elegido) }}</h2>
                <span class="tenue no-imprimir">Haz clic en una clase para moverla, cambiarla o quitarla.</span>
            </div>
            @include('componentes.semana', ['semana' => $semana, 'enlazar' => true])
        </div>

        @if ($vista === 'grupo')
            <div class="no-imprimir">
                <div class="panel">
                    <h2>Agregar clase a {{ $elegido->nombre }}</h2>
                    @include('horarios._formulario', [
                        'accion' => route('horarios.store'),
                        'metodo' => 'POST',
                        'grupo' => $elegido,
                    ])
                </div>

                <div class="panel">
                    <h2>Horas del grupo</h2>
                    <ul class="lista-simple carga">
                        @forelse ($carga as $fila)
                            <li @class(['carga__completa' => $fila['completa']])>
                                <span>{{ $fila['materia']->nombre }}</span>
                                <strong>{{ rtrim(rtrim(number_format($fila['asignadas'], 1), '0'), '.') }} / {{ $fila['requeridas'] }} h</strong>
                                <div class="barra-progreso"><i style="width: {{ $fila['requeridas'] ? min(100, $fila['asignadas'] * 100 / $fila['requeridas']) : 0 }}%"></i></div>
                            </li>
                        @empty
                            <li>No hay materias activas de {{ $elegido->grado }}.º semestre.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endif
@endsection
