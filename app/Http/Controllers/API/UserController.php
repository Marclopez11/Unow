<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Illuminate\Validation\Rule;
use DB;


class UserController extends Controller
{

	/**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Registrar User
     */
    public function register(Request $request)
    {
    	$rules=[
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string|max:100|unique:users',
            'password' => 'required|string|same:confirm_password|min:6',
        ];
    	if($request->hasFile("profile")){
        	$rules["profile"] = 'image|mimes:jpeg,png,jpg,gif,svg|max:5120';
        }
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $user=DB::table('users')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->get('password')),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        /*Guardar imagen*/
        if($request->file("profile")){
			$imageName = time().'.'.$request->profile->extension();
        	$request->profile->move(public_path('images/users/'), $imageName);
        	DB::table('users')->where("id",$user)->update(["profile"=>$imageName]);
        }
        return response()->json([
            'message' => 'Usuario registrado con éxito'
        ], 200);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //credenciales incorrectas
        if (! $token = auth('api')->attempt($validator->validated())) {
            return response()->json(['error' => 'No autorizado'], 401);
        }
        return $this->createNewToken($token);
    }

 	/**
     * get user profile
     */
    public function get_profile()
    {

    	$user = auth('api')->user();
    	$user->profile=(file_exists(public_path('images/users').'/'.$user->profile))?url('images/users').'/'.$user->profile:NULL;
    	return response()->json(['user' => $user], 200);

    }


    /**
     * update user profile
     */
    public function update_profile(Request $request)
    {

    	$user = auth('api')->user();
    	$rules=[
            'name' => ['required','string','between:2,100'],
            'email' => ['required','string','email','max:100',Rule::unique('users')->ignore($user->id)],
            'phone' => ['required','string','max:100',Rule::unique('users')->ignore($user->id)],
            'password' => ['string','min:6']
        ];
        if($request->hasFile("profile")){
        	$rules["profile"] = 'image|mimes:jpeg,png,jpg,gif,svg|max:5120';
        }
    	$validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $update_data=[
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'updated_at' => date("Y-m-d H:i:s")
        ];
        if($request->has('password') && !empty($request->get('password'))){
        	$update_data['password']=bcrypt($request->get('password'));
        }
        try{
        	$query=DB::table('users')->where("id",$user->id)->update($update_data);
    	}catch(\Illuminate\Database\QueryException $ex){
    		return response()->json(['error' => $ex->getMessage()], 401);
    	}catch(\Exception){
    		return response()->json(['error' => "Algo salió mal"], 401);
    	}
        if($query){
        	$user = DB::table('users')->select("id","name","email","phone","profile")->where("id",$user->id)->first();

        	/*Guardar imagen de perfil*/
        	if($request->hasFile("profile")){
        		$old_image=$user->profile;
				$imageName = time().'.'.$request->profile->extension();
        		$request->profile->move(public_path('images/users/'), $imageName);
        		DB::table('users')->where("id",$user->id)->update(["profile"=>$imageName]);
        		if(file_exists(public_path('images/users').'/'.$old_image)){
        			@unlink(public_path('images/users').'/'.$old_image);
        		}
        	}
        	$user = DB::table('users')->select("id","name","email","phone","profile")->where("id",$user->id)->first();
        	//Generar URL
            $user->profile=(!empty($user->profile) && file_exists(public_path('images/users').'/'.$user->profile))?url('images/users').'/'.$user->profile:NULL;
        	return response()->json(['message' => 'Perfil del usuario actualizado con éxito','user' => $user], 200);
        }else{
        	return response()->json(['error' => 'No se pudo actualizar el perfil de usuario'], 401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 720,
            //'user' => auth()->user()
        ]);
    }
}
