<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Service\ProductFilter;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request, ProductFilter $product)
    {
        $opject = $product->getData();
        $products = $opject['products'];
        $variations = $opject['variations'];
        return view('products.index',compact('products','variations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $checkSku = Product::where('sku',$request->sku)->orderBy('id','DESC')->first();
        if($checkSku!=""){
            return 'Invalid SKU';
        }
        else{
            $product = Product::create($request->only('title', 'sku', 'description'));
            if ($request->product_variant) {
                $variants = $request->product_variant;

                if($variants!=""){
                    foreach ($variants as $key => $variant) {
                        $tags = $variants[$key]['tags'];
                        if($variants[$key]!=""){
                            foreach ($tags as $k => $tag) {
                                if(isset($variants[$key]['option']) && $variants[$key]['option'] > 0){
                                    $vdata['variant'] = $tags[$k];
                                    $vdata['variant_id'] = $variants[$key]['option'];
                                    $vdata['product_id'] = $product->id;
                                    ProductVariant::insert($vdata);
                                }
                            }
                        }
                    }

                    if ($request->product_variant_prices) {
                        $pvPrices = $request->product_variant_prices;
                        if(count($pvPrices) > 0){
                            foreach ($pvPrices as $key => $pvPrice) {
                                if($pvPrice!=""){

                                    $variationTitle = rtrim($pvPrices[$key]['title'],'/');
                                    list($v1, $v2, $v3) = explode('/',$variationTitle);

                                    $getFirstVariantId = ProductVariant::where([['variant',$v1],['product_id',$product->id]])->orderBy('id','DESC')->first();
                                    if($getFirstVariantId!=""){
                                        $variationoneid1 = $getFirstVariantId->id;
                                    }
                                    else{
                                        $variationoneid1 = 0;
                                    }

                                    $getSecondVariantId = ProductVariant::where([['variant',$v2],['product_id',$product->id]])->orderBy('id','DESC')->first();
                                    if($getSecondVariantId!=""){
                                        $variationoneid2 = $getSecondVariantId->id;
                                    }
                                    else{
                                        $variationoneid2 = 0;
                                    }

                                    $getThirdVariantId = ProductVariant::where([['variant',$v3],['product_id',$product->id]])->orderBy('id','DESC')->first();
                                    if($getThirdVariantId!=""){
                                        $variationoneid3 = $getThirdVariantId->id;
                                    }
                                    else{
                                        $variationoneid3 = 0;
                                    }

                                    if($pvPrices[$key]!=""){
                                        $vpdata['product_variant_one'] = $variationoneid1;
                                        $vpdata['product_variant_two'] = $variationoneid2;
                                        $vpdata['product_variant_three'] = $variationoneid3;
                                        $vpdata['price'] = $pvPrices[$key]['price'];
                                        $vpdata['stock'] = $pvPrices[$key]['stock'];
                                        $vpdata['product_id'] = $product->id;
                                        ProductVariantPrice::insert($vpdata);
                                    }
                                }
                            }
                        }
                    }
                }

            }

            return 'success';
        }

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        $productid = $product->id;
        $getproducts = Product::all();
        return view('products.edit', compact('variants','productid','getproducts'));
    }

    public function editApi($id)
    {
        $getproducts = Product::find($id);
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product = Product::where('id',$product->id)->update($request->only('title', 'sku', 'description'));
        if ($request->product_variant) {
            $variants = $request->product_variant;

            if($variants!=""){
                foreach ($variants as $key => $variant) {
                    $tags = $variants[$key]['tags'];
                    if($variants[$key]!=""){
                        foreach ($tags as $k => $tag) {
                            if(isset($variants[$key]['option']) && $variants[$key]['option'] > 0){
                                $vdata['variant'] = $tags[$k];
                                $vdata['variant_id'] = $variants[$key]['option'];
                                $vdata['product_id'] = $product->id;
                                ProductVariant::where('product_id',$product->id)->update($vdata);
                                //$lastentryid =  DB::getPdo()->lastInsertId();
                            }
                        }
                    }
                }

                if ($request->product_variant_prices) {
                    $pvPrices = $request->product_variant_prices;
                    if(count($pvPrices) > 0){
                        foreach ($pvPrices as $key => $pvPrice) {
                            if($pvPrice!=""){

                                $variationTitle = rtrim($pvPrices[$key]['title'],'/');
                                list($v1, $v2, $v3) = explode('/',$variationTitle);


                                ////////////// get first variant id
                                $getFirstVariantId = ProductVariant::where([['variant',$v1],['product_id',$product->id]])->orderBy('id','DESC')->first();
                                if($getFirstVariantId!=""){
                                    $variationoneid1 = $getFirstVariantId->id;
                                }
                                else{
                                    $variationoneid1 = 0;
                                }

                                ////////////// get second variant id
                                $getSecondVariantId = ProductVariant::where([['variant',$v2],['product_id',$product->id]])->orderBy('id','DESC')->first();
                                if($getSecondVariantId!=""){
                                    $variationoneid2 = $getSecondVariantId->id;
                                }
                                else{
                                    $variationoneid2 = 0;
                                }

                                ////////////// get third variant id
                                $getThirdVariantId = ProductVariant::where([['variant',$v3],['product_id',$product->id]])->orderBy('id','DESC')->first();
                                if($getThirdVariantId!=""){
                                    $variationoneid3 = $getThirdVariantId->id;
                                }
                                else{
                                    $variationoneid3 = 0;
                                }

                                if($pvPrices[$key]!=""){
                                    $vpdata['product_variant_one'] = $variationoneid1;
                                    $vpdata['product_variant_two'] = $variationoneid2;
                                    $vpdata['product_variant_three'] = $variationoneid3;
                                    $vpdata['price'] = $pvPrices[$key]['price'];
                                    $vpdata['stock'] = $pvPrices[$key]['stock'];
                                    $vpdata['product_id'] = $product->id;
                                    ProductVariantPrice::where('product_id',$product->id)->update($vpdata);
                                }
                            }
                        }
                    }
                }
            }

        }

        return 'success';

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
    public function copyListing($id){
        $skus = $invoice_number = rand(100, 999).$id;
        $products = Product::find($id);
        $newproduct = $products->replicate();
        $newproduct->sku = $skus;
        $newproduct->save();
        $lastProID = $newproduct->id;

        $productsGallery = ProductImage::where('product_id',$id)->first();
        if($productsGallery!=""){
            $newproductG = $productsGallery->replicate();
            $newproductG->product_id = $lastProID;
            $newproductG->save();
        }

        $productsVariant = ProductVariant::where('product_id',$id)->first();
        $newproductV = $productsVariant->replicate();
        $newproductV->product_id = $lastProID;
        $newproductV->save();

        $productsVariantPrice = ProductVariantPrice::where('product_id',$id)->first();
        $newproductVP = $productsVariantPrice->replicate();
        $newproductVP->product_id = $lastProID;
        $newproductVP->save();

        self::message('success', 'Data Added successfully Done');
        return redirect()->back();
    }
}
