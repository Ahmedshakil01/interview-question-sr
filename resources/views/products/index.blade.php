@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products ( {{$products->total()}} )</h1>
    </div>

    @include ('components.flash-messages')

    <div class="card">
        <form action="{{ route('product.index') }}" method="get" class="card-header">
            @csrf
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control" value="{{ request()->get('title') }}">
                </div>
                <div class="col-md-2">
                    <select name="variant" class="form-control">
                        @if(request()->get('variant')!="")
                            <option value="{{ request()->get('variant') }}">{{ request()->get('variant') }}</option>
                            <option value="">Select</option>
                        @else
                            <option value="">Select</option>
                        @endif

                        @foreach($variations as $variation)
                            <optgroup label="{{ $variation->title }}">
                                @foreach($variation->allvariant as $variation)
                                    <option value="{{ $avar->variant }}">{{ $avar->variant }}</option>
                                @endforeach
                            </optgroup>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control" value="{{ request()->get('price_from') }}">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control" value="{{ request()->get('price_to') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="customdate" placeholder="Date" class="form-control" value="{{ request()->get('customdate') }}">
                </div>
                <div class="col-md-3">
                    <a href="{{ route('product.index') }}" class="btn btn-danger float-right" style="margin-left:5px;"><i class="fa fa-sync"></i> Clear Filter</a>
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table" width="100%">
                    <thead>
                    <tr>
                        <th width="9%">#</th>
                        <th width="14%">Title</th>
                        <th width="34%">Description</th>
                        <th width="35%">Variant</th>
                        <th width="8%">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($products as $product)
                        @php
                            $date = \Carbon\Carbon::createFromTimeStamp(strtotime($product->created_at))->diffForHumans();
                        @endphp
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->title }} <br> Created at : {{ $date }}</td>
                            <td>{{ $product->description }}</td>
                            <td>
                                @foreach($product->variationPrice as $variant)

                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                        <dt class="col-sm-3 pb-0">

                                            {{ $variant->firstVariation->variant.' / '.$variant->secondVariation->variant.' / '.$variant->thirdVariation->variant }}

                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4 pb-0">Price : {{ number_format($variant->price,2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock : {{ number_format($variant->stock) }}</dd>
                                            </dl>
                                        </dd>

                                    </dl>
                                @endforeach
                                <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                    <a href="{{ route('admin.copyproduct', $product->id) }}" class="btn btn-warning">Copy Listing</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5">{{ $products->links() }}</td>
                    </tr>
                    </tfoot>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} out of {{$products->total()}}</p>
                </div>
                <div class="col-md-2">

                </div>
            </div>
        </div>
    </div>

@endsection
