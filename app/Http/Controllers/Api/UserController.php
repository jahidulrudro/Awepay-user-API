<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Transformers\Api\ResponseTransformer;
use App\Http\Transformers\Api\UserTransformer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/users",
     * summary="Show All Users",
     * description="Show All Users",
     * operationId="ShowAllUsers",
     * tags={"User"},
     * security={{"bearer_token":{}}},
     *  @OA\Response(
     *    response=422,
     *    description="Unprocessed entity",
     *    @OA\JsonContent(
     *       type ="object",
     *       properties={
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="data", type="object"),
     *       }
     *        )
     *     )
     * )
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(): JsonResponse
    {
        try {
            $users = User::all();
            return (new ResponseTransformer)->success(
                (new UserTransformer)->response($users),
                'All users listed successfully.'
            );
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return (new ResponseTransformer)->error('', $e->getMessage(), 500);
        }
    }

    /**
     *  @OA\Post(
     * path="/api/v1/users",
     * summary="Create User",
     * description="Create user with fullname, email, password, phone and age",
     * operationId="CreateUser",
     * tags={"User"},
     * security={{"bearer_token":{}}},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"fullName","email","password"},
     *   @OA\Property(property="fullName", type="string", format="text", example="Mohammad Jahidul alam"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessed entity",
     *    @OA\JsonContent(
     *       type ="object",
     *       properties={
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="data", type="object"),
     *       }
     *        )
     *     )
     * )
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'email' => 'required|string|max:255|email:rfc,dns|unique:users',
            'phone' => 'unique:users|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'age' => 'numeric|min:0|max:130',
        ]);

        if ($validator->fails()) {
            return (new ResponseTransformer)->error('Validation Error.', $validator->errors(), 400);
        }

        try {
            $user = User::firstOrCreate([
                'email' => $request->email,
                'fullName' => $request->fullName,
                'phone' => $request->phone,
                'age' => $request->age
            ]);

            Cache::put('User[' . $user->id . ']', $user, 15);

            $success['id'] =  $user->id;

            return (new ResponseTransformer)->success($success, 'User created successfully.', 201);
        } catch (ModelNotFoundException $e) {
            return (new ResponseTransformer)->error('User not found.', $e, 404);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/users/{id}",
     * summary="Show User",
     * description="Show User",
     * operationId="ShowUser",
     * tags={"User"},
     * security={{"bearer_token":{}}},
     *    @OA\Parameter(
     *         description="ID of user to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessed entity",
     *    @OA\JsonContent(
     *       type ="object",
     *       properties={
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="data", type="object"),
     *       }
     *        )
     *     )
     * )
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function show(Request $request, User $user): JsonResponse
    {
        $request->merge(['id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'id' => 'required | integer',
        ]);

        if ($validator->fails()) {
            return (new ResponseTransformer)->error('Validation Error.', $validator->errors(), 400);
        }

        $key = 'User[' . $user->id . ']';

        try {
            if (Cache::has($key)) {
                $user = Cache::get($key);
            } else {
                $user = User::findOrFail($user->id);
                Cache::put($key, $user, 15);
            }

            return (new ResponseTransformer)->success(
                (new UserTransformer)->response($user),
                'User found successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return (new ResponseTransformer)->error('User not found.', $e, 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *@return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     * path="/api/v1/users/{id}",
     * summary="Update User",
     * description="Update User",
     * operationId="UpdateUser",
     * tags={"User"},
     * security={{"bearer_token":{}}},
     *    @OA\Parameter(
     *         description="ID of user to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *  @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"fullName","email","password"},
     *       @OA\Property(property="fullName", type="string", format="text", example="Mohammad Jahidul alam"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="phone", type="string", format="text", example="0172518616"),
     *      @OA\Property(property="age", type="integer", format="number", example="30"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessed entity",
     *    @OA\JsonContent(
     *       type ="object",
     *       properties={
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="data", type="object"),
     *       }
     *        )
     *     )
     * )
     *  @param Request $request
     *  @param User
     *  @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->merge(['id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'fullName' => 'required|string|max:255',
            'email' => [
                'required',
                'max:255',
                'email:rfc,dns',
                Rule::unique('users')->ignore($user->id, 'id'),
            ],
            'phone' => [
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                Rule::unique('users')->ignore($user->id, 'id'),
            ],

            'age' => 'numeric | min:0 | max: 130',
        ]);

        if ($validator->fails()) {
            return (new ResponseTransformer)->error('Validation Error.', $validator->errors(), 400);
        }

        try {
            User::findOrFail($user->id)
                ->update();

            Cache::put('User[' . $user->id . ']', $user, 15);

            return (new ResponseTransformer)->success(
                (new UserTransformer)->response($user),
                'User updated successfully.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return (new ResponseTransformer)->error($e, 'User Not found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     * path="/api/v1/users/{id}",
     * summary="Delete User",
     * description="Delete User",
     * operationId="DeleteUser",
     * tags={"User"},
     * security={{"bearer_token":{}}},
     *    @OA\Parameter(
     *         description="ID of user to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessed entity",
     *    @OA\JsonContent(
     *       type ="object",
     *       properties={
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="data", type="object"),
     *       }
     *        )
     *     )
     * )
     * @OA\Response(
     *    response=404,
     *    description="User Not found",
     *    @OA\JsonContent(
     *       type ="object",
     *        properties={
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="message", type="string"),
     *        @OA\Property(property="data", type="object"),
     *       }
     *        )
     *     )
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        $request->merge(['id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'id' => 'required | integer',
        ]);

        if ($validator->fails()) {
            return (new ResponseTransformer)->error('Validation Error.', $validator->errors(), 400);
        }

        try {
            $user = User::findOrFail($user->id);
            $user->delete();
            return (new ResponseTransformer)->success(
                (new UserTransformer)->response($user),
                'User deleted successfully.',
                410
            );
        } catch (ModelNotFoundException $e) {
            return (new ResponseTransformer)->error('User not found.', $e, 404);
        }
    }

    /**
     *  @OA\Post(
     * path="/api/v1/users/search",
     * summary="Search User",
     * description="Search user with email, phone",
     * operationId="SearchUser",
     * tags={"User"},
     * security={{"bearer_token":{}}},
     * @OA\RequestBody(
     *    required=true,
     *    description="Search User",
     *    @OA\JsonContent(
     *  type ="object",
     *         properties={
     *   @OA\Property(property="email", type="string", format="email", example="test@test.com"),
     *       @OA\Property(property="phone", type="string", format="text", example = "0172518616"),
     *       @OA\Property(property="order_by", type="string", format="text", example="email"),
     *      @OA\Property(property="order", type="string", format="text", example="asc"),
     * }
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessed entity",
     *    @OA\JsonContent(
     *       type ="object",
     *       properties={
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="data", type="object"),
     *       }
     *        )
     *     )
     * )
     * Store a newly created resource in storage.
     *
     * Display a listing of the resource.
     * @param Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, User $user): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'string|max:255|email:rfc,dns',
                'phone' => 'regex:/^([0-9\s\-\+\(\)]*)$/',
                'order_by' => 'in:email,phone | string',
                'order' => 'in:asc,desc | string',
                'limit' => 'integer',
            ]);

            if ($validator->fails()) {
                return (new ResponseTransformer)->error('Validation Error.', $validator->errors(), 400);
            }

            $users = User::query();

            if ($request->has('email') || $request->has('phone')) {
                $users = $users->where('email', 'LIKE', "%{$request->get('email')}%")
                    ->orwhere('phone', 'LIKE', "%{$request->get('phone')}%");
            }

            if ($request->has('order_by')) {
                $users = $users->orderBy($request->get('order_by'), $request->get('order') ?? 'desc');
            }

            $users = $users->simplePaginate($request->get('limit') ?? 10);

            return (new ResponseTransformer)->success(
                (new UserTransformer)->response($users),
                'Search data generated successfully.'
            );

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return (new ResponseTransformer)->error($e);
        }
    }
}
