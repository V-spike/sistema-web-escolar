{{-- Formación del docente: se usa en crear y editar. Ayuda a decidir qué materias puede dar. --}}
<fieldset class="formulario__grupo">
    <legend>Formación</legend>

    @foreach (['licenciatura' => 'Licenciatura', 'especialidad' => 'Especialidad', 'maestria' => 'Maestría', 'doctorado' => 'Doctorado'] as $campo => $etiqueta)
        <label for="{{ $campo }}" class="campo__etiqueta">{{ $etiqueta }}</label>
        <input id="{{ $campo }}" name="{{ $campo }}" type="text" maxlength="150" value="{{ old($campo, $docente->$campo ?? '') }}"
               class="campo @error($campo) campo--error @enderror">
        @error($campo) <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
    @endforeach
</fieldset>
