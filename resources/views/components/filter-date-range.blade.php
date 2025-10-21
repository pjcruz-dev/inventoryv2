@props([
    'nameFrom',
    'nameTo',
    'labelFrom' => 'From',
    'labelTo' => 'To',
    'colSize' => 'col-md-3'
])

<div class="{{ $colSize }}">
    <label class="form-label fw-semibold">{{ $labelFrom }}</label>
    <input type="date" 
           name="{{ $nameFrom }}" 
           class="form-control" 
           value="{{ request($nameFrom) }}">
</div>
<div class="{{ $colSize }}">
    <label class="form-label fw-semibold">{{ $labelTo }}</label>
    <input type="date" 
           name="{{ $nameTo }}" 
           class="form-control" 
           value="{{ request($nameTo) }}">
</div>

