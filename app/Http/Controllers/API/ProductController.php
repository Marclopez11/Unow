<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use DB;

class ProductController extends Controller
{
    /**
     * Create a new ProductController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Create a product.
     *
     * @return json
     */
    public function add(Request $request){
    	$user = auth('api')->user();
    	$validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|between:2,2000',
            'quantity' => 'required|integer',
            'category_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $category=DB::table('categories')->where("id",$request->get("category_id"))->first();
        if(!isset($category->id)){
        	return response()->json(['message' => 'No se encontró la categoría'], 400);
        }
        $product=DB::table('products')->insertGetId([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        /*save image*/
        if($request->file("image")){
			$imageName = time().'.'.$request->image->extension();
        	$request->image->move(public_path('images/products/'), $imageName);
        	DB::table('products')->where("id",$product)->update(["image"=>$imageName]);
        }
        return response()->json([
            'message' => 'Producto creado con éxito'
        ], 200);
    }

    /**
     * Update a product.
     *
     * @return json
     */

    public function update($id,Request $request){
    	$user = auth('api')->user();
    	$product=DB::table('products')->select("id","name","category_id","description","quantity","image","created_at","updated_at")->where("id",$id)->where("user_id", $user->id)->first();
    	if(!isset($product->id)){
    		return response()->json(['message' => 'No se encontró el producto'], 400);
    	}
    	$validator = Validator::make($request->all(), [
            'name' =>['required','string','between:2,100'],
            'quantity' => ['required','integer'],
            'description' => ['required','between:2,2000'],
            'category_id' => ['required'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $category=DB::table('categories')->where("id",$request->get("category_id"))->first();
        if(!isset($category->id)){
        	return response()->json(['message' => 'No se encontró la categoría'], 400);
        }
        DB::table('products')->where("user_id", $user->id)->where('id',$id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        /*save profile image*/
        if($request->hasFile("image")){
        	$old_image=$product->image;
			$imageName = time().'.'.$request->image->extension();
        	$request->image->move(public_path('images/products/'), $imageName);
        	DB::table('products')->where("id",$product->id)->update(["image"=>$imageName]);
        	if(file_exists(public_path('images/products').'/'.$old_image)){
        		@unlink(public_path('images/products').'/'.$old_image);
        	}
        }
        $product=DB::table('products')->select("id","name","category_id","description","quantity","image","created_at","updated_at")->where("id",$id)->where("user_id", $user->id)->first();
        $product->image=(file_exists(public_path('images/products').'/'.$product->image))?url('images/products').'/'.$product->image:NULL;
        return response()->json([
            'message' => 'Producto actualizado con éxito',
            'product'=>$product
        ], 200);
    }

    /**
     * Delete a product.
     *
     * @return json
     */

    public function delete($id,Request $request){
    	$user = auth('api')->user();
    	$product=DB::table('products')->where("user_id",$user->id)->where("id",$id)->delete();
        return response()->json([
            'message' => 'Producto eliminado con éxito'
        ], 200);
    }

    /**
     * get a product.
     *
     * @return json
     */
    public function view($id,Request $request){
    	$user = auth('api')->user();
    	$product=DB::table('products')->select("id","user_id","name","description","quantity","created_at","image","updated_at")->where("user_id",$user->id)->where("id",$id)->first();
    	if(!isset($product->id)){
    		return response()->json(['message' => 'No se encontró el producto'], 400);
    	}
    	$product->image=(!empty($product->image) && file_exists(public_path('images/products').'/'.$product->image))?url('images/products').'/'.$product->image:NULL;
        return response()->json([
            'message' => 'El producto se obtiene con éxito',
            'category' => $product
        ], 200);
    }


	/**
     * Get all products.
     *
     * @return json
     */
    public function list(Request $request){
    	$user = auth('api')->user();
    	$products=DB::table('products')->select("id","user_id","name","description","quantity","created_at","updated_at")->where("user_id",$user->id)->get()->toArray();
    	$data=[];
    	foreach ($products as $product) {
    		$product->image=(!empty($product->image) && file_exists(public_path('images/products').'/'.$product->image))?url('images/products').'/'.$product->image:NULL;
    		$data[]=$product;
    	}
        return response()->json([
            'message' => 'Los productos se obtienen con éxito',
            'categories' => $data
        ], 200);
    }

    /**
     * Update a product quantity.
     *
     * @return json
     */

    public function update_quantity($id,Request $request){
    	$user = auth('api')->user();
    	$product=DB::table('products')->select("id","name","category_id","description","quantity","created_at","updated_at")->where("id",$id)->where("user_id", $user->id)->first();
    	if(!isset($product->id)){
    		return response()->json(['message' => 'No se encontró el producto'], 400);
    	}
    	$validator = Validator::make($request->all(), [
            'quantity' => ['required','integer'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        DB::table('products')->where("user_id", $user->id)->where('id',$id)->update([
            'quantity' => $request->quantity,
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        $product=DB::table('products')->select("id","name","category_id","description","quantity","created_at","updated_at")->where("id",$id)->where("user_id", $user->id)->first();
        return response()->json([
            'message' => 'Cantidad de producto actualizada con éxito',
            'product'=>$product
        ], 200);
    }
}
