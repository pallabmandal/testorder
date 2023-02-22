<?php

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\ResponseHandler;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Helpers\CustomValidator;
use Auth;

class AuthController extends Controller
{
    use AuthenticatesUsers;
    use ResponseHandler;

    public function login(Request $request)
    {    
        $inputs = $request->all();

        $validator_rules = [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ];
        $validate_result = CustomValidator::validator($inputs, $validator_rules);

        if($validate_result['code']!== 200){
            return ResponseHandler::buildUnsuccessfulValidationResponse($validate_result);
        }

        if ($this->attemptLogin($request)) {
            $user = Auth::user();
            $user['token'] = $user->createToken(env('APP_KEY'))->accessToken;

            return $this->buildSuccess(true, $user, 'User logged in successfully', Response::HTTP_OK);
        } else {
            throw new \App\Exceptions\AuthException("Invalid Credentials, please try again", 401);
        }
    }
}
