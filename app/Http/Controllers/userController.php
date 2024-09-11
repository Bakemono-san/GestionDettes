<?php

namespace App\Http\Controllers;

use App\Enums\StateEnum;
use App\Facades\UserServiceFacade;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\userForClientRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Endpoints for managing users"
 * )
 */
class userController extends Controller
{
    use RestResponseTrait;
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="List all users",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter by role",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"oui", "non"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserCollection")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(Request $request)
    {
        $roleFilter = function ($query) use ($request) {
            if ($request->has('role')) {
                $role = $request->input('role');
                $query->where('roles.name', $role);
            }
        };

        $actives = $request->has('active');

        if ($actives && $request->input('active') == 'oui') {
            $field = 'users.etat'; // Specify the table name for 'etat'
            $value = 'true';
            $sign = '=';
        } else if ($actives && $request->input('active') == 'non') {
            $field = 'users.etat'; // Specify the table name for 'etat'
            $value = 'false';
            $sign = '=';
        } else {
            $field = 'users.id'; // Specify the table name for 'id'
            $value = 0;
            $sign = '>=';
        }

        $users = QueryBuilder::for(User::class)
            ->allowedIncludes(['role'])
            ->join('roles', 'roles.id', '=', 'users.role_id') // Corrected the table name to 'users'
            ->where($roleFilter)
            ->where($field, $sign, $value) // Specify the table name for the dynamic field
            ->get();

        return $this->sendResponse(new UserCollection($users), StateEnum::SUCCESS, 'Users fetched successfully', 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function store(StoreUserRequest $request)
    {

        $user = UserServiceFacade::create($request->only('nom', 'prenom', 'login', 'role_id', 'etat', 'password'));
        return compact('user');
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get user by ID",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse([], StateEnum::ECHEC, "User not found", 404);
        }

        return $this->sendResponse(new UserResource($user), StateEnum::SUCCESS, 'User fetched successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     */
     /**
     * @OA\Post(
     *     path="/users/{id}",
     *     summary="Update user details",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $request->validate();

        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse([], StateEnum::ECHEC, "User not found", 404);
        }

        $user->update($request->all());
        return $this->sendResponse(new UserCollection($user), StateEnum::SUCCESS, 'User updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        $user->delete();
        return $this->sendResponse([], StateEnum::SUCCESS, 'User deleted successfully', 204);
    }

    /**
     * @OA\Post(
     *     path="/users/for-client",
     *     summary="Create a user for a client",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/userForClientRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User and client created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user", ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="client", ref="#/components/schemas/ClientResource")
     *         )
     *     )
     * )
     */
    public function createUserForClient(userForClientRequest $request){
        
        $client = Client::find($request->input('client_id'));


        $user = User::create($request->except('client_id'));
        $client->user()->associate($user);
        $client->save();
        return compact('user', 'client');
    }
}
