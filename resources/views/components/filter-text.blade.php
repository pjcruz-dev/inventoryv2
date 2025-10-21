@props([
    'name',
    'label',
    'placeholder' => '',
    'colSize' => 'col-md-3',
    'list' => null
])

<div class="{{ $colSize }}">
    <label class="form-label fw-semibold">{{ $label }}</label>
    <input type="text" 
           name="{{ $name }}" 
           class="form-control" 
           placeholder="{{ $placeholder }}" 
           value="{{ request($name) }}"
           @if($list) list="{{ $list }}" @endif>
</div>

