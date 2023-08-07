<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_create_user_api()
    {
    	$numbers = range(1, 1000);
		shuffle($numbers);
        $payload=[
        	'name' => "unow",
            'email' => "demouser_".uniqid()."@gmail.com",
            'phone' =>  $numbers[0]."837536".$numbers[1],
            'password' => "123456",
            'confirm_password' => "123456"
        ];
        $response=$this->json('POST','api/user/register',$payload)->assertStatus(200);
    }

    public function test_login_user_api()
    {
        $payload=[
        	'email' => "unow@gmail.com",
            'password' => "123456"
        ];
        $response=$this->json('POST','api/user/login',$payload)->assertStatus(200);
    }
}
