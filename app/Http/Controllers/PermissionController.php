<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
// use Illuminate\validation\Rule;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware() : array
    {
        return [
            new Middleware('permission:view permissions', only:['index']),
            new Middleware('permission:edit permissions', only:['edit']),
            new Middleware('permission:create permissions', only:['create']),
            new Middleware('permission:delete permissions', only:['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::orderBy('created_at','DESC')->paginate(25);
        return view('permissions.list',[
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions|min:3'
        ]);

        if($validator->passes()){
            Permission::create(['name' => $request->name]);
            return redirect()->route('permissions.list')->with('success','Permission added successfully');
        }
        else {
            return redirect()->back()->withInput()->withErrors($validator);
        }
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
        $permission = Permission::findorFail($id);

        return view('permissions.edit',[
            'permission' => $permission
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $permission = Permission::findorFail($id);

        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions,name,'.$id.',id'
        ]);
        // Rule::unique('permissions')->ignore($permission->id)

        if($validator->passes()){
            
            $permission->name = $request->name;
            $permission->save();
            return redirect()->route('permissions.list')->with('success','Permission updated successfully');
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

        $id = $request->id;
    
        try {
            $permission = Permission::find($id);
            if($permission !== null){
                $permission->delete();
                return response()->json([
                    'status' => true,
                    'success_message' => 'Permission Deleted Successfully'
                ],200);
                
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in Permission.destroy',
                'error' =>$e->getMessage()
            ],400);    
        }

    }
}
