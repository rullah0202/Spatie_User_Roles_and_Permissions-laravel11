<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{

    public static function middleware() : array
    {
        return [
            new Middleware('permission:view users', only:['index']),
            new Middleware('permission:edit users', only:['edit']),
            // new Middleware('permission:create roles', only:['create']),
            // new Middleware('permission:delete roles', only:['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.list',[
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findorFail($id);
        $roles = Role::orderBy('name','ASC')->get();

        $hasRoles = $user->roles->pluck('id');
        return view('users.edit',[
            'user' => $user,
            'roles' => $roles,
            'hasRoles' => $hasRoles 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findorFail($id);

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$id.',id'

        ]);
        // Rule::unique('Users')->ignore($user->id)

        if($validator->passes()){
            
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            $user->syncRoles($request->role);

            return redirect()->route('users.list')->with('success','User updated successfully');
        }
        else {
            return redirect()->back()->withInput()->withErrors($validator);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
