<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Whoops\Run;

class UserController extends Controller
{
    //
    public function addUser(Request $req)
    {
        if($this->checkUser($req->email)){
            return response()->json(["Result"=> "This user already exist!"], 401);
        }else{
            $user = new User;
            $user->first_name = $req->first_name;
            $user->last_name = $req->last_name;
            $user->email = $req->email;
            $user->password = Hash::make($req->password);
            $user->address = $req->address;
            if($user->save()){
                return response()->json(["result"=> "User saved"], 201);
            }else{
                return response()->json(["result"=> "User not saved"], 404);
            }
        }
    }

    public function login(Request $req)
    {
        $user = User::where('email', $req->email)->first();
        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json([
                'result' => ['These credentials do not match our records.']
            ], 404);
        }
        $token = $user->createToken('my-app-token', ['server:update'])->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        
        return response()->json($response, 201);
    }

    public function getUser(Request $req)
    {
        $user = User::find($req->key);
        if($user != null){
            return response()->json($user); 
        }else{
            return response()->json(["Result"=> "User not found"], 404);
        }
    }

    public function searchUser(Request $req)
    {
        $user = User::where("first_name", "like", "%".$req->keyword."%")
                ->orWhere("last_name", "like", "%".$req->keyword."%")
                ->get();
        if($user != null){
            return response()->json($user, 201);
        }else{
            return response()->json(["Result"=> "No Result found!"], 404);
        }
    }

    public function updatePassword(Request $req)
    {
        $user = User::find($req->id);
        $user->password = $req->password;
        if($user->save()){
            return response()->json(["Result"=> "Password Updated"], 201);
        }else{
            return response()->json(["Result"=> "Something went wrong!"], 402);
        }
    }

    public function forgetPassword(Request $req)
    {
        // if($this->checkUser($req->email) && $req->validate(['email' => 'required|email'])){
        //     // Password::sendResetLink($req->email);
        //     $user = User::where("email", "=",$req->email);
        //     // return $user->sendPasswordResetLink() ? 
        //     //         response()->json(['result' => 'reset link sent to your email'], 201): 
        //     //         response()->json(['result' => 'email not sent please try again later'], 201);



        //     $status = Password::sendResetLink(
        //         $req->only('email')
        //     );

        //     return $status === Password::RESET_LINK_SENT
        //         ? back()->with(['status' => __($status)])
        //         : back()->withErrors(['email' => __($status)]);

        //     // $response = [
        //     //     'result' => 'reset link sent to your email'
        //     // ];
        //     // return response()->json($response, 201);
        // }else{
        //     return response()->json(["Result"=> "Email is not exist!"], 404);
        // }

    }

    // check exist user
    private function checkUser($email){
        $user= User::where('email', $email)->first();
        if($user != null){
            return true;
        }else{
            return false;
        }
    }

    protected function sendResetLinkResponse(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
        'email' => "required|email"
        ]);
        if ($validator->fails()) {
        return response(['errors'=>$validator->errors()->all()], 422);
        }
        $response =  Password::sendResetLink($input);
        echo $response;
        if($response == Password::RESET_LINK_SENT){
        $message = "Mail send successfully";
        }else{
        $message = "Email could not be sent to this email address";
        }
        //$message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : GLOBAL_SOMETHING_WANTS_TO_WRONG;
        $response = ['data'=>'','message' => $message];
        return response($response, 200);
    }

    protected function sendResetResponse(Request $request){
    //password.reset
    $input = $request->only('email','token', 'password', 'password_confirmation');
    $validator = Validator::make($input, [
    'token' => 'required',
    'email' => 'required|email',
    'password' => 'required|confirmed|min:8',
    ]);
    if ($validator->fails()) {
    return response(['errors'=>$validator->errors()->all()], 422);
    }
    $response = Password::reset($input, function ($user, $password) {
        $user->forceFill([
        'password' => Hash::make($password)
        ])->save();
        //$user->setRememberToken(Str::random(60));
        event(new PasswordReset($user));
    });
    if($response == Password::PASSWORD_RESET){
        $message = "Password reset successfully";
    }else{
        $message = "Email could not be sent to this email address";
    }
        $response = ['data'=>'','message' => $message];
        return response()->json($response);
    }
}


// 195.229.241.222
