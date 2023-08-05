<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use DB;

class CategoryController extends Controller
{
    /**
     * Create a new CategoryController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Create a category.
     *
     * @return json
     */
    public function add(Request $request){

        //Prevenir la inyección SQL
    	$validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100|unique:categories',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $user=DB::table('categories')->insert([
            'name' => $request->name,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        return response()->json([
            'message' => 'Categoría creada con éxito'
        ], 200);
    }

    /**
     * Update a category.
     *
     * @return json
     */

    public function update($id,Request $request){
    	$category=DB::table('categories')->select("id","name","created_at","updated_at")->where("id",$id)->first();
    	if(!isset($category->id)){
    		return response()->json(['message' => 'No se encontró la categoría'], 400);
    	}
    	$validator = Validator::make($request->all(), [
            'name' => ['required','string','between:2,100',Rule::unique('categories')->ignore($id)],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        DB::table('categories')->where('id',$id)->update([
            'name' => $request->name,
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        $category=DB::table('categories')->select("id","name","created_at","updated_at")->where("id",$id)->first();
        return response()->json([
            'message' => 'Categoría actualizada con éxito',
            'category'=>$category
        ], 200);
    }

    /**
     * Delete a category.
     *
     * @return json
     */

    public function delete($id,Request $request){
    	$category=DB::table('categories')->where("id",$id)->first();
    	if(!isset($category->id)){
    		return response()->json(['message' => 'No se encontró la categoría'], 400);
    	}
    	$category=DB::table('categories')->where("id",$id)->delete();
    	DB::table('products')->where("category_id",$id)->update([
            'category_id' => NULL,
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        return response()->json([
            'message' => 'Categoría eliminada con éxito'
        ], 200);
    }

    /**
     * get a category.
     *
     * @return json
     */
    public function view($id,Request $request){
    	$category=DB::table('categories')->select("id","name","created_at","updated_at")->where("id",$id)->first();
    	if(!isset($category->id)){
    		return response()->json(['message' => 'No se encontró la categoría'], 400);
    	}
        return response()->json([
            'message' => 'Categoría obtenidas con éxito',
            'category' => $category
        ], 200);
    }


	/**
     * Get all categories.
     *
     * @return json
     */
    public function list(Request $request){
    	$categories=DB::table('categories')->select("id","name","created_at","updated_at")->get()->toArray();
        return response()->json([
            'message' => 'Categoría obtenidas con éxito',
            'categories' => $categories
        ], 200);
    }
}
