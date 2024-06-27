@extends('layouts.admin')

@section('title', 'Manage Product')
@section('content')
    <div class="container">
        <h1>Manage Product: {{ $product->name }}</h1>

        <form method="POST" action="{{ route('admin.products.manageUpdate', $product->id) }}">
            @csrf
            @method('POST')
            
            @foreach ($attributes as $attribute)
                <div class="form-group">
                    <label for="{{ 'attribute_' . $attribute->id }}">{{ $attribute->name }}</label>
                    
                    @php
                        $attributeValueId = $product->attributes()->where('attribute_id', $attribute->id)->value('attribute_value_id');
                    @endphp

                    @if ($attributeValues[$attribute->id]->isEmpty())
                        <input type="text" class="form-control" id="{{ 'attribute_' . $attribute->id }}" name="{{ 'attributes['.$attribute->id.'][attribute_value_id]' }}" value="{{ $attributeValueId }}">
                    @else
                        <select class="form-control" id="{{ 'attribute_' . $attribute->id }}" name="{{ 'attributes['.$attribute->id.'][attribute_value_id]' }}">
                            <option value="">NULL</option> <!-- Пустой вариант -->
                            @foreach ($attributeValues[$attribute->id] as $value)
                                <option value="{{ $value->id }}" @if ($value->id == $attributeValueId) selected @endif>{{ $value->value }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>

    </div>
@endsection
