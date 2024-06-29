

<!-- Нижний блок: комментарии -->
<div class="row">
    <div class="col-md-12">
        @foreach($product->reviews as $review)
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title">{{ $review->user->name }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Rating: {{ $review->rating }}/5</h6>
                    <p class="card-text">{{ $review->comment }}</p>
                    <p class="card-text"><small class="text-muted">{{ $review->created_at->format('d M Y') }}</small></p>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Форма для написания комментария -->
<div class="row mt-4">
    <div class="col-md-12">
        <h2>Add a Review</h2>
        <form action="{{ route('catalog.product.review', $product->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="rating">Rating</label>
                <select name="rating" id="rating" class="form-control" required>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Terrible</option>
                </select>
            </div>
            <div class="form-group">
                <label for="comment">Comment</label>
                <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>
</div>