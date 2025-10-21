@props([
    'sortOptions' => [],
    'defaultSort' => 'created_at',
    'defaultOrder' => 'desc'
])

<div class="col-md-2">
    <label class="form-label fw-semibold">Sort By</label>
    <select name="sort_by" class="form-select">
        @foreach($sortOptions as $value => $label)
            <option value="{{ $value }}" {{ request('sort_by', $defaultSort) == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
<div class="col-md-2">
    <label class="form-label fw-semibold">Order</label>
    <select name="sort_order" class="form-select">
        <option value="asc" {{ request('sort_order', $defaultOrder) == 'asc' ? 'selected' : '' }}>Ascending</option>
        <option value="desc" {{ request('sort_order', $defaultOrder) == 'desc' ? 'selected' : '' }}>Descending</option>
    </select>
</div>

