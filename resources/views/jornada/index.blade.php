@extends('layouts.app')

@section('titulo', 'Jornada y recesos')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Jornada y recesos</h1>
<p class="tenue">Las clases se acomodan con cualquier hora dentro de la jornada, sin encimarse con un receso. No se acepta un cambio que deje clases ya acomodadas fuera.</p>

<div class="rejilla-2">
    <div class="panel">
        <h2>Así se ve la semana</h2>
        @include('componentes.semana', ['semana' => $semana, 'chica' => true])
    </div>

    <div>
        <div class="panel">
            <h2>Horario del turno</h2>
            <form method="POST" action="{{ route('jornada.update') }}">
                @csrf
                @method('PUT')
                <div class="formulario__fila">
                    <div>
                        <label for="hora_entrada" class="campo__etiqueta">Entrada</label>
                        <input id="hora_entrada" name="hora_entrada" type="time" class="campo @error('hora_entrada') campo--error @enderror"
                               value="{{ old('hora_entrada', $jornada->hora_entrada_corta) }}" required>
                    </div>
                    <div>
                        <label for="hora_salida" class="campo__etiqueta">Salida</label>
                        <input id="hora_salida" name="hora_salida" type="time" class="campo @error('hora_salida') campo--error @enderror"
                               value="{{ old('hora_salida', $jornada->hora_salida_corta) }}" required>
                    </div>
                </div>
                @error('hora_entrada') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
                @error('hora_salida') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

                <label for="minutos_por_hora" class="campo__etiqueta">Minutos de una hora clase</label>
                <input id="minutos_por_hora" name="minutos_por_hora" type="number" min="30" max="60" class="campo @error('minutos_por_hora') campo--error @enderror"
                       value="{{ old('minutos_por_hora', $jornada->minutos_por_hora) }}" required>
                <span class="campo__ayuda">Sirve para contar las horas de cada materia y de cada plaza.</span>
                @error('minutos_por_hora') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

                <div class="formulario__acciones">
                    <button type="submit" class="btn btn--primario">Guardar jornada</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Recesos</h2>
            <ul class="lista-simple">
                @forelse ($recesos as $receso)
                    <li>
                        <span><strong>{{ $receso->nombre }}</strong> · {{ $receso->rango }}</span>
                        <form method="POST" action="{{ route('jornada.recesos.destroy', $receso) }}" onsubmit="return confirm('¿Quitar este receso?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--fantasma btn--chico">Quitar</button>
                        </form>
                    </li>
                @empty
                    <li>No hay recesos.</li>
                @endforelse
            </ul>

            <form method="POST" action="{{ route('jornada.recesos.store') }}" style="margin-top:1rem">
                @csrf
                <label for="nombre" class="campo__etiqueta">Nuevo receso</label>
                <input id="nombre" name="nombre" type="text" maxlength="40" placeholder="Receso 3" class="campo @error('nombre') campo--error @enderror" value="{{ old('nombre') }}" required>
                <div class="formulario__fila">
                    <div>
                        <label for="receso_inicio" class="campo__etiqueta">Inicio</label>
                        <input id="receso_inicio" name="hora_inicio" type="time" class="campo @error('hora_inicio') campo--error @enderror" value="{{ old('hora_inicio') }}" required>
                    </div>
                    <div>
                        <label for="receso_fin" class="campo__etiqueta">Fin</label>
                        <input id="receso_fin" name="hora_fin" type="time" class="campo @error('hora_fin') campo--error @enderror" value="{{ old('hora_fin') }}" required>
                    </div>
                </div>
                @error('nombre') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
                @error('hora_inicio') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
                @error('hora_fin') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
                <div class="formulario__acciones">
                    <button type="submit" class="btn btn--secundario">Agregar receso</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
