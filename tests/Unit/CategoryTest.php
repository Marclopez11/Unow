<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class CategoryTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_category_create()
    {
    	$numbers = range(1, 1000);
		shuffle($numbers);
        $payload=[
        	'name' => "category".$numbers[0]
        ];
        $user = \App\Models\User::first();
    	$response = $this->actingAs($user, 'api')->json('POST','api/category/add',$payload)->assertStatus(200);
    }

    public function test_category_update()
    {
    	$numbers = range(1, 1000);
		shuffle($numbers);
		$category=DB::table("categories")->first();
        $payload=[
        	'name' => "category_update".$numbers[0]
        ];
        $user = \App\Models\User::first();
    	$response = $this->actingAs($user, 'api')->json('PUT','api/category/update/'.$category->id,$payload)->assertStatus(200);
    }

    public function test_category_delete()
    {
    	
		$category=DB::table("categories")->orderBy('id', 'desc')->first();
        $user = \App\Models\User::first();
    	$response = $this->actingAs($user, 'api')->json('DELETE','api/category/delete/'.$category->id,[])->assertStatus(200);
    }
}
