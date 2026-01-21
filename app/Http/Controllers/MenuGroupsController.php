<?php

namespace App\Http\Controllers;

 
use App\Models\MenuGroup;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
 
use App\Helpers\LogHelper;
 

class MenuGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:menugroup-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:menugroup-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:menugroup-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:menugroup-delete', ['only' => ['destroy']]);
    }

 

    public function updatePositions(Request $request)
    {
        $positions = $request->input('positions'); 
        foreach ($positions as $index => $id) {
            $menu_group = MenuGroup::find($id);
            if ($menu_group) {
                $menu_group->position = $index + 1;  
                $menu_group->save(); 
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $title = "Halaman Menu Group";
        $subtitle = "Menu Menu Group";
        
        $query = MenuGroup::orderBy('position', 'asc');
         
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $data_menu_group = $query->paginate(20); 
    
        return view('menu_group.index', compact('data_menu_group', 'title', 'subtitle'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $title = "Halaman Tambah Menu Group";
        $subtitle = "Menu Tambah Menu Group";
        $data_permission =  Permission::pluck('name', 'name')->all();
        return view('menu_group.create', compact('title', 'subtitle', 'data_permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:menu_groups,name',
            'permission_name' => 'required',
            'status' => 'required',
            'position' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.unique' => 'Nama sudah terdaftar.',
            'permission_name.required' => 'Nama Permission wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'position.required' => 'Urutan wajib diisi.',
        ]);
    
        $menuGroup = MenuGroup::create($request->all());
    
        LogHelper::logAction('menu_groups', $menuGroup->id, 'Create', null, $menuGroup->toArray());
    
        return redirect()->route('menu_groups.index')
            ->with('success', 'Menu Group berhasil dibuat.');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\MenuGroup  $menu_group
     * @return \Illuminate\Http\Response
     */
    public function show($id): View

    {
        $title = "Halaman Lihat Menu Group";
        $subtitle = "Menu Lihat Menu Group";
        $data_menu_group = MenuGroup::find($id);
        $data_permission =  Permission::pluck('name', 'name')->all();
        return view('menu_group.show', compact('data_menu_group', 'title', 'subtitle', 'data_permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MenuGroup  $menu_group
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $title = "Halaman Edit Menu Group";
        $subtitle = "Menu Edit Menu Group";
        $data_menu_group = MenuGroup::find($id);
        $data_permission =  Permission::pluck('name', 'name')->all();
        return view('menu_group.edit', compact('data_menu_group', 'title', 'subtitle', 'data_permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuGroup  $menu_group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:menu_groups,name,' . $id,
            'permission_name' => 'required',
            'status' => 'required',
            'position' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.unique' => 'Nama sudah terdaftar.',
            'permission_name.required' => 'Nama Permission wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'position.required' => 'Urutan wajib diisi.',
        ]);

        $menuGroup = MenuGroup::find($id);

        if (!$menuGroup) {
            return redirect()->route('menu_groups.index')
                ->with('error', 'Menu Group tidak ditemukan.');
        }

        // Simpan data lama sebelum update
        $oldData = $menuGroup->toArray();

        // Update data
        $menuGroup->update($request->all());

        // Simpan log
        LogHelper::logAction('menu_groups', $menuGroup->id, 'Update', $oldData, $menuGroup->fresh()->toArray());

        return redirect()->route('menu_groups.index')
            ->with('success', 'Menu Group berhasil diperbaharui.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuGroup  $menu_group
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menuGroup = MenuGroup::find($id);

        if (!$menuGroup) {
            return redirect()->route('menu_groups.index')
                ->with('error', 'Menu Group tidak ditemukan.');
        }

        // Simpan data lama sebelum dihapus
        $oldData = $menuGroup->toArray();

        // Hapus menu group
        $menuGroup->delete();

        // Simpan log
        LogHelper::logAction('menu_groups', $id, 'Delete', $oldData, null);

        return redirect()->route('menu_groups.index')
            ->with('success', 'Menu Group berhasil dihapus.');
    }
}
