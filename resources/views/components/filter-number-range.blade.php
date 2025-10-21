@props([
    'nameMin',
    'nameMax',
    'labelMin' => 'Min',
    'labelMax' => 'Max',
    'placeholderMin' => '0.00',
    'placeholderMax' => '999999.99',
    'step' => '0.01',
    'colSize' => 'col-md-3'
])

<div class="{{ $colSize }}">
    <label class="form-label fw-semibold">{{ $labelMin }}</label>
    <input type="number" 
           name="{{ $nameMin }}" 
           class="form-control" 
           placeholder="{{ $placeholderMin }}" 
           step="{{ $step }}" 
           value="{{ request($nameMin) }}">
</div>
<div class="{{ $colSize }}">
    <label class="form-label fw-semibold">{{ $labelMax }}</label>
    <input type="number" 
           name="{{ $nameMax }}" 
           class="form-control" 
           placeholder="{{ $placeholderMax }}" 
           step="{{ $step }}" 
           value="{{ request($nameMax) }}">
</div>

