<?php

namespace App\Http\Controllers;

 
use App\Models\MenuGroup;
use App\Models\MenuItem;
use App\Models\Permission;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;


use App\Helpers\LogHelper;

class MenuItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:menuitem-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:menuitem-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:menuitem-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:menuitem-delete', ['only' => ['destroy']]);
    }

    

    public function updatePositions(Request $request)
    {
        $positions = $request->input('positions');  
        $parentId = $request->input('parent_id');  
     
        foreach ($positions as $index => $itemId) {
            $menu_item = MenuItem::find($itemId);
    
            if ($menu_item) {
                if ($menu_item->parent_id === null) {  
                   
                    $menu_item->position = $index + 1;
                    $menu_item->save();
                } elseif ($menu_item->parent_id == $parentId) {  
                    
                    $menu_item->position = $index + 1;
                    $menu_item->save();
                }
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
        $title = "Halaman Menu Item";
        $subtitle = "Menu Menu Item";
        
        $query = MenuItem::orderBy('position', 'asc');
     
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }
    
        $data_menu_item = $query->paginate(50);
    
        return view('menu_item.index', compact('data_menu_item', 'title', 'subtitle'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create(): View
    // {
    //     $title = "Halaman Tambah Menu Item";
    //     $subtitle = "Menu Tambah Menu Item";
    //     $data_menu_group = MenuGroup::pluck('name', 'id')->all();
    //     $data_permission = Permission::pluck('name', 'id')->all();
    //     $data_menu_items = MenuItem::where('status', 'Aktif')->pluck('name', 'id')->all();
         
    //     $data_routes = Route::pluck('name', 'name')->all();
    
    //     return view('menu_item.create', compact('title', 'subtitle', 'data_permission', 'data_menu_group', 'data_menu_items', 'data_routes'));
    // }
    
    public function create(): View
{
    $title = "Halaman Tambah Menu Item";
    $subtitle = "Menu Tambah Menu Item";
    
    $data_menu_group = MenuGroup::pluck('name', 'id')->toArray(); // OK, atau tanpa toArray()
    $data_permission = Permission::pluck('name', 'id')->toArray(); // OK
    $data_menu_items = MenuItem::where('status', 'Aktif')->pluck('name', 'id')->toArray(); // FIXED
    $data_routes = Route::pluck('name', 'name')->toArray(); // FIXED

    return view('menu_item.create', compact('title', 'subtitle', 'data_permission', 'data_menu_group', 'data_menu_items', 'data_routes'));
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
            'name' => 'required|unique:menu_items,name',
            'permission_name' => 'required',
            'status' => 'required',
            'icon' => 'required',
            'route' => 'required',
            'position' => 'required|integer',
            'menu_group_id' => 'required|exists:menu_groups,id',
            'parent_id' => 'nullable|exists:menu_items,id',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'icon.required' => 'Icon wajib diisi.',
            'route.required' => 'Route wajib diisi.',
            'name.unique' => 'Nama sudah terdaftar.',
            'permission_name.required' => 'Nama Permission wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'position.required' => 'Urutan wajib diisi.',
            'menu_group_id.required' => 'Menu Group wajib dipilih.',
            'menu_group_id.exists' => 'Menu Group tidak ditemukan.',
            'parent_id.exists' => 'Parent Menu tidak valid.',
        ]);

        $newMenuItem = MenuItem::create($request->all());

        LogHelper::logAction('menu_items', $newMenuItem->id, 'Create', null, $newMenuItem->toArray());

        return redirect()->route('menu_items.index')
            ->with('success', 'Menu Item berhasil dibuat.');
    }






    /**
     * Display the specified resource.
     *
     * @param  \App\MenuItem  $menu_item
     * @return \Illuminate\Http\Response
     */
    public function show($id): View

    {
        $title = "Halaman Lihat Menu Item";
        $subtitle = "Menu Lihat Menu Item";
        $data_menu_item = MenuItem::find($id);
        $data_permission =  Permission::pluck('name', 'name')->all();
        $data_menu_group =  MenuGroup::pluck('name', 'id')->all();
        return view('menu_item.show', compact('data_menu_item', 'title', 'subtitle', 'data_permission', 'data_menu_group'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MenuItem  $menu_item
     * @return \Illuminate\Http\Response
     */
    // public function edit($id): View
    // {
    //     $title = "Halaman Edit Menu Item";
    //     $subtitle = "Menu Edit Menu Item";
    //     $data_menu_item = MenuItem::findOrFail($id);  
    //     $data_permission = Permission::pluck('name', 'name')->all();
    //     $data_menu_group = MenuGroup::pluck('name', 'id')->all();
    //     $data_menu_items = MenuItem::where('id', '!=', $id)->pluck('name', 'id')->all();  
    //     $data_routes = Route::pluck('name', 'name')->all();  
    
    //     return view('menu_item.edit', compact('data_menu_item', 'title', 'subtitle', 'data_permission', 'data_menu_group', 'data_menu_items', 'data_routes'));
    // }
    
    public function edit($id): View
{
    $title = "Halaman Edit Menu Item";
    $subtitle = "Menu Edit Menu Item";
    $data_menu_item = MenuItem::findOrFail($id);  
    $data_permission = Permission::pluck('name', 'name')->toArray(); // FIXED
    $data_menu_group = MenuGroup::pluck('name', 'id')->toArray(); // OK
    $data_menu_items = MenuItem::where('id', '!=', $id)->pluck('name', 'id')->toArray(); // FIXED
    $data_routes = Route::pluck('name', 'name')->toArray(); // OK

    return view('menu_item.edit', compact('data_menu_item', 'title', 'subtitle', 'data_permission', 'data_menu_group', 'data_menu_items', 'data_routes'));
}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MenuItem  $menu_item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'permission_name' => 'required',
            'status' => 'required',
            'icon' => 'required',
            'route' => 'required',
            'position' => 'required|integer',
            'menu_group_id' => 'required|exists:menu_groups,id',
            'parent_id' => 'nullable|exists:menu_items,id',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'icon.required' => 'Icon wajib diisi.',
            'route.required' => 'Route wajib diisi.',
           
            'permission_name.required' => 'Nama Permission wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'position.required' => 'Urutan wajib diisi.',
            'menu_group_id.required' => 'Menu Group wajib dipilih.',
            'menu_group_id.exists' => 'Menu Group tidak ditemukan.',
            'parent_id.exists' => 'Parent Menu tidak valid.',
        ]);

        $menuItem = MenuItem::find($id);

        if (!$menuItem) {
            return redirect()->route('menu_items.index')
                ->with('error', 'Menu Item tidak ditemukan.');
        }

        $oldData = $menuItem->toArray();

        $menuItem->update($request->all());

        LogHelper::logAction('menu_items', $id, 'Update', $oldData, $menuItem->fresh()->toArray());

        return redirect()->route('menu_items.index')
            ->with('success', 'Menu Item berhasil diperbaharui.');
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuItem  $menu_item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menuItem = MenuItem::find($id);

        if (!$menuItem) {
            return redirect()->route('menu_items.index')
                ->with('error', 'Menu Item tidak ditemukan.');
        }

        $oldData = $menuItem->toArray();

        $menuItem->delete();

        LogHelper::logAction('menu_items', $id, 'Delete', $oldData, null);

        return redirect()->route('menu_items.index')
            ->with('success', 'Menu Item berhasil dihapus.');
    }
}
