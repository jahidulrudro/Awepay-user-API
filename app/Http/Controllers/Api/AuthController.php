<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\Api\ResponseTransformer as ResponseTransformer;
use App\Models\AuthUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/register",
     * summary="Register",
     * description="Register with fullname, email, password, phone and age",
     * operationId="Registration",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name","email","password"},
     *   @OA\Property(property="name", type="string", format="text", example="Mohammad Jahidul alam"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="")
     *        )
     *     )
     * )
     * User Registration Method
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email:rfc,dns|unique:auth_users',
            'password' => 'required|min:6|string',
        ]);

        if ($validator->fails()) {
            return (new ResponseTransformer)->error('Validation Error.', $validator->errors(), 400);
        }

        try {
            $user = AuthUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
        } catch (ModelNotFoundException $e) {
            return (new ResponseTransformer)->error('User not found', $e, 500);
        }

        $success['token'] =  $user->createToken('apiAccessToken')->accessToken;
        $success['name'] =  $user->name;

        return (new ResponseTransformer)->success($success, 'Registered successfully.', 201);
    }

    /**
     * @OA\Post(
     * path="/api/v1/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     * User Login Method
     * @return \Illuminate\Http\Response
     */

    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:6|string',
        ]);

        if ($validator->fails()) {
            return (new ResponseTransformer)->error('Validation Error.', $validator->errors(), 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('apiAccessToken')->accessToken;
            $success['name'] =  $user->name;

            return (new ResponseTransformer)->success($success, 'Login successfully.', 200);
        }

        return (new ResponseTransformer)->error('Unauthorised.', ['error' => 'Unauthorised'], 401);
    }
}
