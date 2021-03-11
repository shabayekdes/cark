@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a href="{{ route('products.create') }}" class="btn btn-primary">Upload Product</a>
                    <a href="{{ route('products.edit') }}" class="btn btn-success">Edit Product</a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
