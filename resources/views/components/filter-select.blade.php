@props([
    'name',
    'label',
    'options' => [],
    'selected' => '',
    'placeholder' => 'All',
    'colSize' => 'col-md-3'
])

<div class="{{ $colSize }}">
    <label class="form-label fw-semibold">{{ $label }}</label>
    <select name="{{ $name }}" class="form-select">
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            @if(is_numeric($value))
                <option value="{{ $text }}" {{ request($name) == $text ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @else
                <option value="{{ $value }}" {{ request($name) == $value ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endif
        @endforeach
    </select>
</div>

