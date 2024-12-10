<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{

    public static function middleware() : array
    {
        return [
            new Middleware('permission:view roles', only:['index']),
            new Middleware('permission:edit roles', only:['edit']),
            new Middleware('permission:create roles', only:['create']),
            new Middleware('permission:delete roles', only:['destroy']),
        ];
    }
        /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('name','ASC')->paginate(25);
        return view('roles.list',[
            'roles' => $roles
        ]);
    }
        /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name','ASC')->get();
        return view('roles.create',[
            'permissions' => $permissions
        ]);
    }

        /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:roles|min:3'
        ]);

        if($validator->passes()){
            $role = Role::create(['name' => $request->name]);

            if(!empty($request->permission)){
                foreach ($request->permission as $name) {
                    $role->givePermissionTo($name);
                }
            }

            return redirect()->route('roles.list')->with('success','Roles added successfully');
        }
        else {
            return redirect()->back()->withInput()->withErrors($validator);
        }
    }

        /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findorFail($id);
        $hasPermissions = $role->permissions->pluck('name');
        $permissions = Permission::orderBy('name','ASC')->get();

        return view('roles.edit',[
            'permissions' => $permissions,
            'hasPermissions' => $hasPermissions,
            'role' => $role
        ]);

    }    /**
    * Update the specified resource in storage.
    */
   public function update(Request $request, string $id)
   {

        $role = Role::findorFail($id);

        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:roles,name,'.$id.',id'
        ]);
        // Rule::unique('permissions')->ignore($permission->id)

        if($validator->passes()){
            
            $role->name = $request->name;
            $role->save();
            if(!empty($request->permission)){
                $role->syncPermissions($request->permission);
            } else {
                $role->syncPermissions([]);
            }

            return redirect()->route('roles.list')->with('success','Role updated successfully');
        }
        else {
            return redirect()->back()->withInput()->withErrors($validator);
        }
   }

       /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        $role = Role::find($request->id);

        if($role == null){
            session()->flash('error','Role not found.');
            return response()->json([
                'status' => false
            ]);
        }
        $role->delete();
        session()->flash('success','Role deleted successfully.');
        return response()->json([
            'status' => true
        ]);

    }


}
