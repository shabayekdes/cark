@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Upload Products</div>

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
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                    @endif
                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                    @endif
                    <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="brandSelect">Brand</label>
                            <select class="form-control" id="brandSelect" name="brand">
                              <option value="">select one</option>
                              @foreach ($taxonomies as $taxonomy)
                                <option value="{{ $taxonomy->term_taxonomy_id }}">{{ $taxonomy->term->name }}</option>
                              @endforeach
                            </select>
                          </div>
                        <div class="form-group">
                            <label for="productFileLabel">Product File</label>
                            <input type="file" class="form-control" id="productFileLabel" name="product"
                                aria-describedby="productHelp">
                            <small id="productHelp" class="form-text text-muted">We'll never share your email with
                                anyone else.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
