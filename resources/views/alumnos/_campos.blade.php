@php $alumno = $alumno ?? null; @endphp
<label for="name" class="campo__etiqueta">Nombre completo</label>
<input id="name" name="name" type="text" maxlength="255" value="{{ old('name', $alumno?->name) }}" class="campo @error('name') campo--error @enderror" required autofocus>
@error('name') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

<div class="formulario__fila">
    <div>
        <label for="matricula" class="campo__etiqueta">Matrícula</label>
        <input id="matricula" name="matricula" type="text" maxlength="20" value="{{ old('matricula', $alumno?->matricula) }}" class="campo @error('matricula') campo--error @enderror" required>
    </div>
    <div>
        <label for="grupo_id" class="campo__etiqueta">Grupo</label>
        <select id="grupo_id" name="grupo_id" class="campo @error('grupo_id') campo--error @enderror" required>
            <option value="">Elige…</option>
            @foreach ($grupos as $grupo)
                <option value="{{ $grupo->id }}" @selected((int) old('grupo_id', $alumno?->grupo_id) === $grupo->id)>{{ $grupo->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>
@error('matricula') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
@error('grupo_id') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

<label for="email" class="campo__etiqueta">Correo electrónico</label>
<input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $alumno?->email) }}" class="campo @error('email') campo--error @enderror" required>
@error('email') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

<label for="password" class="campo__etiqueta">Contraseña</label>
<input id="password" name="password" type="password" autocomplete="new-password" class="campo @error('password') campo--error @enderror" @required(! $alumno)>
@if ($alumno) <span class="campo__ayuda">Déjala vacía para no cambiarla.</span> @endif
@error('password') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

<label for="password_confirmation" class="campo__etiqueta">Confirma la contraseña</label>
<input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="campo" @required(! $alumno)>
