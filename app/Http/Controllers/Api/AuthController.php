<?php

namespace App\Http\Controllers\Api;

use App\Helpers\StripeHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();
            $user = User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'address'=> $request->address,
                'password'=> Hash::make($request->password),
            ]);

            $stripe = new StripeHelper($user->id);
            $stripe->payment('10',$request->payment_method);
            DB::commit();
            $token = $user->createToken('sanctum_auth');
            return response()->json(['message' => "User created successfully",'token' => $token]);
        } catch (\Exception $e) {
            dd($e);
        }
        

    }

    public function user(Request $request){
        $user = User::find(auth()->id());
        if($request->isMethod('put')){
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
            ]);
            $user = $user->update([
                'name'=> $request->name,
                'email'=> $request->email,
                'address'=> $request->address,
            ]);
            return response()->json(['message' => "User updated successfully"]);
        }
        return response()->json(['user' => auth()->user()]);
    }

    public function logout(Request $request){
        $user = User::find(auth()->id());
        $user->tokens()->delete();
        return response()->json(['message' => 'Logout Successfully']);
    }
}
