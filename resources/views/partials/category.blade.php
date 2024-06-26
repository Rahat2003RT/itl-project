<li class="list-group-item">
    <a href="{{ route('catalog.filter', ['category_name' => $category->name]) }}">
        {{ $category->name }} ({{ $category->countProducts() }})
    </a>
    @if($category->children->isNotEmpty())
        <ul class="list-group mt-2">
            @foreach($category->children as $childCategory)
                @include('partials.category', ['category' => $childCategory])
            @endforeach
        </ul>
    @endif
</li>
