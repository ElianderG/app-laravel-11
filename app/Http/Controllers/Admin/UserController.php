<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
* @OA\Info(
* title="Swagger with Laravel",
* version="1.0.0",
* )
* @OA\SecurityScheme(
* type="http",
* securityScheme="bearerAuth",
* scheme="bearer",
* bearerFormat="JWT"
* )
*/

class UserController extends Controller
{
    /**
     * @OA\Get(
     *   path="/users",
     *   summary="Get a list of users",
     *   tags={"users"},
     *   @OA\Response(
     *     response=200,
     *     description="List of users",
     *   ),
     * )
     */
    public function index()
    {
        $users = User::paginate(); // User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * @OA\Get(
     *   path="/users/create",
     *   summary="Show the form to create a new user",
     *   tags={"users"},
     *   @OA\Response(
     *     response=200,
     *     description="Form for creating a user",
     *   ),
     * )
     */

    public function create()
    {
        return view('admin.users.create');        
    }

    /**
     * @OA\Post(
     *   path="/users",
     *   summary="Create a new user",
     *   tags={"users"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="email", type="string"),
     *       @OA\Property(property="password", type="string"),
     *     ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="User created",
     *   ),
     * )
     */

    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());
        
        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * @OA\Get(
     *   path="/users/{user}/edit",
     *   summary="Show the form to edit a user",
     *   tags={"users"},
     *   @OA\Parameter(
     *     name="user",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Form for editing a user",
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found",
     *   ),
     * )
     */

    public function edit(string $id)
    {
        //$user = User::where('id', '=', $id)->first();
        //$user = User::where('id', $id)->first();  // ->firstOrFail();
        if (!$user = User::find($id)){
            return redirect()->route('users.index')->with('message', 'Usuário não encontrado');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * @OA\Put(
     *   path="/users/{user}",
     *   summary="Update a user by ID",
     *   tags={"users"},
     *   @OA\Parameter(
     *     name="user",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="email", type="string"),
     *       @OA\Property(property="password", type="string"),
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User updated",
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found",
     *   ),
     * )
     */

    public function update(UpdateUserRequest $request, string $id)
    {
        if (!$user = User::find($id)){
            return back()->with('message', 'Usuário não encontrado');
        }
        $data = $request->only('name', 'email');
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()
        ->route('users.index')
        ->with('success', 'Usuário editado com sucesso!');
    }

    /**
     * @OA\Get(
     *   path="/users/{user}",
     *   summary="Get a single user by ID",
     *   tags={"users"},
     *   @OA\Parameter(
     *     name="user",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User details",
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found",
     *   ),
     * )
     */

    public function show(string $id)
    {
        if (!$user = User::find($id)) {
            return redirect()->route('user.index')->with('message', 'Usuário não encontrado');
        }

        return view('admin.users.show', compact ('user'));
    }

    /**
     * @OA\Delete(
     *   path="/users/{user}/destroy",
     *   summary="Delete a user by ID",
     *   tags={"users"},
     *   @OA\Parameter(
     *     name="user",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User deleted",
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found",
     *   ),
     * )
     */

    public function destroy(string $id)
    {
        // if(Gate::denies('is-admin')){
        //     return back()->with('message', 'Você não é um administrador');            
        // }
        if (!$user = User::find($id)) {
            return redirect()->route('users.index')->with('message', 'Usuário não encontrado');
        }

        if(Auth::user()->id === $user->id){
            return back()->with('message', 'Você não pode deletar o seu próprio perfil'); 
        }

        $user->delete();

        return redirect()
        ->route('users.index')
        ->with('success', 'Usuário deletado com sucesso');
    }
}
