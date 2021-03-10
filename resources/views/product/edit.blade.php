@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Products</div>

                <div class="card-body">
                    
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('products.update') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        @method('PUT')

                        <div class="form-group">
                          <label for="productFileLabel">Product File</label>
                          <input type="file" class="form-control" id="productFileLabel" name="product" aria-describedby="productHelp">
                          <small id="productHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                      </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection