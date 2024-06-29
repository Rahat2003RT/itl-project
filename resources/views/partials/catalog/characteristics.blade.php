<!-- Блок общих характеристик -->
@if ($generalAttributes && $generalAttributes->isNotEmpty())
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Общие характеристики</h2>
            <ul>
                @foreach ($generalAttributes as $attribute)
                    <li>{{ $attribute->name }}: {{ $attribute->values->where('id', $attribute->pivot->attribute_value_id)->value('value') }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- Блок технических характеристик -->
@if ($technicalAttributes && $technicalAttributes->isNotEmpty())
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Технические характеристики</h2>
            <ul>
                @foreach ($technicalAttributes as $attribute)
                    <li>{{ $attribute->name }}: {{ $attribute->values->value }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- Блок дополнительных характеристик -->
@if ($additionalAttributes && $additionalAttributes->isNotEmpty())
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Дополнительные характеристики</h2>
            <ul>
                @foreach ($additionalAttributes as $attribute)
                    <li>{{ $attribute->name }}: {{ $attribute->pivot->value }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif