@extends('layouts.app')

@section('titulo', 'Disponibilidad')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
@php $dias = config('escolar.dias'); @endphp
<div class="barra-catalogo">
    <div>
        <h1>Disponibilidad de {{ $docente->nombre }}</h1>
        <p class="tenue" style="margin:0">{{ $docente->nombre_completo }} · plaza de {{ $docente->horas_plaza }} h</p>
    </div>
    <a href="{{ route('docentes.index') }}" class="btn btn--secundario btn--chico btn--enlace">Volver a docentes</a>
</div>

<div class="rejilla-2">
    <div class="panel">
        <h2>Semana</h2>
        <p class="tenue">En verde, cuándo puede dar clase. Encima, las clases que ya tiene.</p>
        @include('componentes.semana', ['semana' => $semana, 'chica' => true])
    </div>

    <div>
        <div class="panel">
            <h2>Agregar horario disponible</h2>
            <form method="POST" action="{{ route('docentes.disponibilidad.guardar', $docente) }}">
                @csrf
                <span class="campo__etiqueta">Días</span>
                <div class="dias-elegir">
                    @foreach ($dias as $numero => $nombre)
                        <label><input type="checkbox" name="dias[]" value="{{ $numero }}" @checked(in_array($numero, old('dias', [])))> {{ $nombre }}</label>
                    @endforeach
                </div>
                @error('dias') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
                <div class="formulario__fila">
                    <div>
                        <label for="hora_inicio" class="campo__etiqueta">Desde</label>
                        <input id="hora_inicio" name="hora_inicio" type="time" class="campo" value="{{ old('hora_inicio', $jornada->hora_entrada_corta) }}" required>
                    </div>
                    <div>
                        <label for="hora_fin" class="campo__etiqueta">Hasta</label>
                        <input id="hora_fin" name="hora_fin" type="time" class="campo" value="{{ old('hora_fin', $jornada->hora_salida_corta) }}" required>
                    </div>
                </div>
                @error('hora_inicio') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
                @error('hora_fin') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
                <span class="campo__ayuda">Si se encima con un rango que ya tiene, se juntan en uno.</span>
                <div class="formulario__acciones">
                    <button type="submit" class="btn btn--primario">Agregar</button>
                </div>
            </form>
            <form method="POST" action="{{ route('docentes.disponibilidad.completa', $docente) }}" style="margin-top:.75rem"
                  onsubmit="return confirm('Esto reemplaza su disponibilidad por toda la jornada, de lunes a viernes. ¿Continuar?')">
                @csrf
                <button type="submit" class="btn btn--secundario btn--chico">Disponible toda la jornada</button>
            </form>
        </div>

        <div class="panel">
            <h2>Rangos guardados</h2>
            <ul class="lista-simple">
                @forelse ($rangos as $rango)
                    <li>
                        <span>{{ $dias[$rango->dia] }} · {{ $rango->rango }}</span>
                        <form method="POST" action="{{ route('docentes.disponibilidad.quitar', [$docente, $rango]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--fantasma btn--chico">Quitar</button>
                        </form>
                    </li>
                @empty
                    <li>Sin disponibilidad capturada. Mientras tanto, el horario solo muestra un aviso.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
