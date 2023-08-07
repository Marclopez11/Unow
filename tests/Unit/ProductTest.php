<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ProductTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_create_prouct()
    {
        $numbers = range(1, 1000);
		shuffle($numbers);
		$user = \App\Models\User::first();
		$category = DB::table("categories")->first();
        $payload=[
        	'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => "test_prouct_".$numbers[0],
            'description' => "This is demo project",
            'quantity' => 1
        ];
    	$response = $this->actingAs($user, 'api')->json('POST','api/product/add',$payload)->assertStatus(200);
    }

    /*public function test_update_prouct()
    {
        $numbers = range(1, 1000);
		shuffle($numbers);
		$user = \App\Models\User::first();
		$category = DB::table("categories")->first();
		$product=DB::table("products")->first();
        $payload=[
        	'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => $product->name,
            'description' => $product->description,
            'quantity' =>  $product->quantity
        ];
    	$response = $this->actingAs($user, 'api')->json('POST','api/product/update/'.$product->id,$payload)->assertStatus(200);
    }*/

    public function test_delete_prouct()
    {
		$user = \App\Models\User::first();
		$product=DB::table("products")->orderBy('id', 'desc')->first();
    	$response = $this->actingAs($user, 'api')->json('DELETE','api/product/delete/'.$product->id,[])->assertStatus(200);
    }
}
