<?php
namespace App\Service;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Collection;
use Validator;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;

class ProductFilter
{


	public function __construct(){

	}


	public function getData(){

		$query = Product::with('variationPrice','variationPrice.firstVariation','variationPrice.secondVariation','variationPrice.thirdVariation');

		$products = $this->filter($query);
		$variations = Variant::with('allvariant')->orderBy('id','ASC')->get();
		$data = array('products'=>$products,'variations'=>$variations);

		return $data;
	}

	public function filter($query){
	   $title = request()->title;
	   $variant = request()->variant;
	   $price_from = request()->price_from;
	   $price_to = request()->price_to;
	   $date = request()->customdate;


	   if($title!=""){
	   	 $query->where('title', 'like', "%{$title}%");
	   }

	   if($date!=""){
	   	 $query->whereDate('created_at', date('Y-m-d',strtotime($date)));
	   }

	   if($variant!="" || $variant!=NULL){
	   	 $getProductId = ProductVariant::select('product_id')->where('variant',$variant)->get();
		 foreach($getProductId as $pids){
		 	$producid[] = $pids->product_id;
		 }
	   	 $query->whereIn('id', $producid);
	   }


	   if($price_from!="" && $price_to!=""){
	   	 $getProductId = ProductVariantPrice::select('product_id')->whereBetween('price',[$price_from,$price_to])->get();

		 if(count($getProductId) > 0){
			 foreach($getProductId as $pids){
				$producid[] = $pids->product_id;
			 }
			 $query->whereIn('id', $producid);
		 }
	   }


	   return $query->orderBy('id','DESC')->paginate(10);
	}
}
