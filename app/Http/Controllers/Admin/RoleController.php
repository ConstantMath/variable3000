<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Auth;
//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Session;

class RoleController extends AdminController{

  public function __construct() {
    $this->middleware(['auth']);//isAdmin middleware lets only users with a //specific permission permission to access these resources
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function index() {
    $data = array(
      'page_class' => 'roles-index',
      'page_title' => 'Roles index',
      'page_id'    => 'roles',
      'page_type'  => 'users',

    );
    $roles = Role::all();
    return view('admin.templates.roles-index', compact('roles', 'data'));
  }


  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */

  public function create() {
    $data = array(
      'page_class' => 'roles-edit',
      'page_title' => 'Create a role',
      'page_id'    => 'roles',
      'page_type'  => 'users',

    );
    $role = collect(new Permission);
    $permissions = Permission::all();
    return view('admin.templates.roles-edit', compact('role', 'permissions', 'data'));
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function store(Request $request) {
    //Validate name and permissions field
    $this->validate($request, [
        'name'=>'required|unique:roles|max:10',
        'permissions' =>'required',
        ]
    );

    $name = $request['name'];
    $role = new Role();
    $role->name = $name;
    $permissions = $request['permissions'];
    $role->save();
    //Looping thru selected permissions
    foreach ($permissions as $permission) {
      $p = Permission::where('id', '=', $permission)->firstOrFail();
      //Fetch the newly created role and assign permission
      $role = Role::where('name', '=', $name)->first();
      $role->givePermissionTo($p);
    }
    return redirect()->route('admin.roles.index')->with('created');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) {
      return redirect('roles');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id) {
    $data = array(
      'page_class' => 'roles-edit',
      'page_title' => 'Create a role',
      'page_id'    => 'roles',
      'page_type'  => 'users',

    );
    $role = Role::findOrFail($id);
    $permissions = Permission::all();
    return view('admin.templates.roles-edit', compact('role', 'permissions', 'data'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id) {

      $role = Role::findOrFail($id);//Get role with the given id
  //Validate name and permission fields
      $this->validate($request, [
          'name'=>'required|max:10|unique:roles,name,'.$id,
          'permissions' =>'required',
      ]);

      $input = $request->except(['permissions']);
      $permissions = $request['permissions'];
      $role->fill($input)->save();

      $p_all = Permission::all();//Get all permissions

      foreach ($p_all as $p) {
          $role->revokePermissionTo($p); //Remove all permissions associated with role
      }

      foreach ($permissions as $permission) {
          $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form //permission in db
          $role->givePermissionTo($p);  //Assign permission to role
      }
      return redirect()->route('admin.roles.index')->with('flash_message', 'updated!');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      $role = Role::findOrFail($id);
      $role->delete();
      return redirect()->route('admin.roles.index')->with('flash_message','Role deleted');

  }
}
