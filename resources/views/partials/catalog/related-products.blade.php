<!-- Блок с продуктами из той же коллекции -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="row">
            @foreach($relatedProducts as $relatedProduct)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('storage/' . ($relatedProduct->images->first()->image_url ?? 'default/default-product.png')) }}" class="card-img-top" alt="{{ $relatedProduct->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                            <p class="card-text">{{ $relatedProduct->price }} $</p>
                            <a href="{{ route('catalog.product.show', $relatedProduct->id) }}" class="btn btn-primary">Посмотреть</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>