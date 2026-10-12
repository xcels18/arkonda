<div class="space-y-4">
    @foreach(['name' => ['Nama lengkap', 'text', 'name'], 'email' => ['Email', 'email', 'email']] as $field => [$label, $type, $autocomplete])
        <div><label for="user-{{ $field }}" class="block text-sm font-medium mb-1.5">{{ $label }} <span class="text-red-600" aria-hidden="true">*</span></label>
            <input id="user-{{ $field }}" name="{{ $field }}" type="{{ $type }}" autocomplete="{{ $autocomplete }}" value="{{ old($field, $record?->$field) }}" required maxlength="255" class="ui-input w-full" @if($errors->has($field)) aria-invalid="true" aria-describedby="user-{{ $field }}-error" @endif>
            @error($field)<p id="user-{{ $field }}-error" data-field-error class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    @endforeach
    <div><label for="user-opd_id" class="block text-sm font-medium mb-1.5">OPD</label><select id="user-opd_id" name="opd_id" class="ui-input w-full" aria-describedby="user-opd-help">
        <option value="">Belum ditetapkan</option>@foreach($opds as $opd)<option value="{{ $opd->id }}" @selected(old('opd_id', $record?->opd_id) == $opd->id)>{{ $opd->nama }}</option>@endforeach
    </select><p id="user-opd-help" class="text-xs text-slate-500 mt-1">Petugas perlu OPD agar dapat mengakses arsip kontrak.</p>@error('opd_id')<p data-field-error class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
    <div class="pt-4 border-t border-slate-200"><p class="text-xs text-slate-500 mb-3">Kosongkan kata sandi untuk mempertahankan kata sandi pengguna.</p>
        <label for="user-password" class="block text-sm font-medium mb-1.5">Kata sandi baru</label><input id="user-password" name="password" type="password" minlength="8" autocomplete="new-password" class="ui-input w-full">
        @error('password')<p data-field-error class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div><label for="user-password_confirmation" class="block text-sm font-medium mb-1.5">Konfirmasi kata sandi baru</label><input id="user-password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" class="ui-input w-full"></div>
</div>
