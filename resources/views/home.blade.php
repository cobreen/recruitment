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
                    <div class="products">
                        <div v-for='(product, index) in products' class='product'>
                            <img :src="product.image" alt="">
                            <div class="name">
                                @{{ product.name }}
                            </div>
                            <div class="price">
                                @{{ product.price }}{{ config('app.character') }}
                            </div>
                            <button class="buy" @click='buy(product.id)'>
                                <div>
                                    {{ __('Buy') }}
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        window.token = '{{ $token }}'
    </script>
@endsection