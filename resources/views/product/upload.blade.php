@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Upload Products</div>

                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                          <label for="productFileLabel">Product File</label>
                          <input type="file" class="form-control" id="productFileLabel" name="product" aria-describedby="productHelp">
                          <small id="productHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="form-group">
                            <label for="productImageLabel">Products Images</label>
                            <input type="file" class="form-control" id="productImageLabel" name="thumb" aria-describedby="thumbHelp">
                            <small id="thumbHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                          </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                      </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection