<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
 
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:role-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Role";
        $subtitle = "Menu Role";
        $data_role = Role::orderBy('id', 'DESC')->paginate(5);
        return view('role.index', compact('data_role', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $title = "Halaman Tambah Role";
        $subtitle = "Menu Tambah Role";
        $permission = Permission::get();
        return view('role.create', compact('permission', 'title', 'subtitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.unique' => 'Nama sudah terdaftar.',
            'permission.required' => 'Permission wajib diisi.',
        ]);


        $permissionsID = array_map(
            function ($value) {
                return (int) $value;
            },
            $request->input('permission')
        );


        $role = Role::create(['name' => $request->input('name')]);

        $role->syncPermissions($permissionsID);

      
        LogHelper::logAction('roles', $role->id, 'Create', null, $role->toArray());
      

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dibuat');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $title = "Halaman Lihat Role";
        $subtitle = "Menu Lihat Role";
        $data_role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();

        return view('role.show', compact('data_role', 'rolePermissions', 'title', 'subtitle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $title = "Halaman Edit Role";
        $subtitle = "Menu Edit Role";
        $data_role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = $data_role->permissions->pluck('id')->toArray();


        return view('role.edit', compact('data_role', 'permission', 'rolePermissions', 'title', 'subtitle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $id,
            'permission' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.unique' => 'Nama role sudah terdaftar.',
            'permission.required' => 'Permission wajib diisi.',
        ]);



        $role = Role::find($id);

        if (!$role) {
            return redirect()->route('roles.index')
                ->with('error', 'Role tidak ditemukan');
        }

     

        $role->name = $request->input('name');
        $role->save();

        $permissionsID = array_map(
            function ($value) {
                return (int)$value;
            },
            $request->input('permission')
        );

        $role->syncPermissions($permissionsID);

      
    

        $oldData = $role->toArray();

        $role->update($request->all());


        LogHelper::logAction('roles', $role->id, 'Update', $oldData, $role->fresh()->toArray());

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbaharui');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        // Cek apakah role masih digunakan oleh user melalui pivot table Spatie
        $hasUsers = DB::table('model_has_roles')
            ->where('role_id', $id)
            ->where('model_type', 'App\Models\User')
            ->exists();

        if ($hasUsers) {
            return redirect()->route('roles.index')->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user.');
        }

        $oldData = $role->toArray();

        $role->delete();

        LogHelper::logAction('roles', $id, 'Delete', $oldData, null);


        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }

}
