<div class="space-y-4">
    @foreach(['kode' => ['Kode OPD', 10], 'nama' => ['Nama OPD', 255], 'singkatan' => ['Singkatan', 50]] as $field => [$label, $max])
        <div>
            <label for="opd-{{ $field }}" class="block text-sm font-medium mb-1.5">{{ $label }} <span class="text-red-600" aria-hidden="true">*</span></label>
            <input type="text" id="opd-{{ $field }}" name="{{ $field }}" value="{{ old($field, $record?->$field) }}" maxlength="{{ $max }}" required class="ui-input w-full" @if($errors->has($field)) aria-invalid="true" aria-describedby="opd-{{ $field }}-error" @endif>
            @error($field)<p id="opd-{{ $field }}-error" data-field-error class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    @endforeach
</div>
