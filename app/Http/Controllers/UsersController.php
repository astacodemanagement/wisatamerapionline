<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use Spatie\Permission\Models\Role;

use Hash;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Helpers\LogHelper;

use App\Services\ImageService;

class UsersController extends Controller
{
    protected $imageService;
    function __construct(ImageService $imageService)
    {
        $this->middleware('permission:user-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->imageService = $imageService;
    }


    public function index(Request $request): View
    {
        $title = "Halaman User";
        $subtitle = "Menu User";

        // Ambil semua user dengan relasi roles
        $data_user = User::with('roles')->get();
        $roles = Role::all();


        return view('user.index', compact('data_user', 'title', 'subtitle', 'roles'));
    }




    public function create(): View
    {
        $title = "Halaman Tambah User";
        $subtitle = "Menu Tambah User";
        $roles = Role::pluck('name', 'name');

        return view('user.create', compact('roles',   'title', 'subtitle'));
    }



    public function store(Request $request): RedirectResponse
    {
        try {
            // Aturan validasi
            $rules = [
                'name' => 'required|string|max:255',
                'user' => 'required|string|max:255|unique:users,user',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:8|same:password_confirmation',
                'roles' => 'required|array',
                'birth_place' => 'required|string|max:255',
                'birth_date' => 'required|date',

                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4048',

                // Opsional
                'phone_number' => 'nullable|string|max:20',
                'address_by_card' => 'nullable|string',
                'rt_rw' => 'nullable|string|max:50',
                'subdistrict' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',

            ];


            // Pesan error kustom
            $messages = [
                'name.required' => 'Nama wajib diisi.',
                'user.required' => 'User wajib diisi.',
                'user.unique' => 'User sudah terdaftar.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.same' => 'Password dan konfirmasi password harus sama.',
                'roles.required' => 'Peran wajib dipilih.',
                'birth_place.required' => 'Tempat lahir wajib diisi.',
                'birth_date.required' => 'Tanggal lahir wajib diisi.',


                'image.image' => 'Gambar harus dalam format jpeg, jpg, png, gif, atau webp.',
                'image.mimes' => 'Format gambar harus jpeg, jpg, png, gif, atau webp.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4 MB.',

                'phone_number.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
                'phone_number.string' => 'Nomor telepon harus berupa teks.',
            ];

            // Validasi input
            $this->validate($request, $rules, $messages);

            // Pakai transaksi supaya rollback kalau error
            DB::beginTransaction();

            $input = $request->all();

            // Handle upload gambar
            $input['image'] = $request->hasFile('image')
                ? $this->imageService->handleImageUpload($request->file('image'), 'upload/users')
                : null;


            $input['password'] = Hash::make($input['password']);
            unset($input['password_confirmation']);

            $user = User::create($input);
            $user->assignRole($request->input('roles'));

            LogHelper::logAction('users', $user->id, 'Create', null, $user->toArray());

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User berhasil dibuat.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error saat menambahkan user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan pada database. Silakan coba lagi.')
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan umum saat menambahkan user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')
                ->withInput();
        }
    }




    public function edit($id): View
    {
        $title = "Halaman Edit User";
        $subtitle = "Menu Edit User";
        $data_user = User::with(['roles'])->findOrFail($id);
        $roles = Role::pluck('name', 'name');
        $usersRole = $data_user->roles->pluck('name', 'name')->all();


        return view('user.edit', compact('data_user', 'roles', 'usersRole',  'title', 'subtitle'));
    }




    public function update(Request $request, $id)
    {
        \Log::info('Update method called for user ID: ' . $id . ' at ' . now()->toDateTimeString());

        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'user' => 'required|string|max:255|unique:users,user,' . $user->id,
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|min:8|same:password_confirmation',
                'birth_place' => 'required|string|max:255',
                'birth_date' => 'required|date',
                'phone_number' => 'nullable|string|max:20',
                'address_by_card' => 'nullable|string',
                'rt_rw' => 'nullable|string|max:50',
                'subdistrict' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4048',
            ], [
                'name.required' => 'Nama wajib diisi.',
                'user.required' => 'User wajib diisi.',
                'user.unique' => 'User sudah terdaftar.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'phone_number.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
                'phone_number.string' => 'Nomor telepon harus berupa teks.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.same' => 'Password dan konfirmasi password harus sama.',
                'birth_place.required' => 'Tempat lahir wajib diisi.',
                'birth_date.required' => 'Tanggal lahir wajib diisi.',
                'image.image' => 'Gambar harus dalam format jpeg, jpg, png, gif, atau webp.',
                'image.mimes' => 'Format gambar harus jpeg, jpg, png, gif, atau webp.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 4 MB.',
            ]);

            $input = $request->all();

            // Handle image upload
            if ($request->hasFile('image')) {
                $input['image'] = $this->imageService->handleImageUpload(
                    $request->file('image'),
                    'upload/users',
                    $user->image
                );
            } else {
                $input['image'] = $user->image;
            }



            // Handle password
            if ($request->filled('password')) {
                $input['password'] = Hash::make($input['password']);
            } else {
                unset($input['password']);
            }

            unset($input['password_confirmation']);
            unset($input['documents']); // Remove documents from user update input
            $oldData = $user->toArray();
            $user->update($input);

            LogHelper::logAction('users', $user->id, 'Update', $oldData, $user->fresh()->toArray());

            // Set session flash message
            session()->flash('success', 'User berhasil diperbarui.');

            // Return JSON response for AJAX
            return response()->json([
                'message' => 'User berhasil diperbarui.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating user ID: ' . $id . ' - ' . $e->getMessage());
            session()->flash('error', 'Gagal memperbarui user.');
            return response()->json(['error' => 'Gagal memperbarui user.'], 500);
        }
    }



    public function destroy($id): RedirectResponse
    {
        \Log::info('Destroy method called for user ID: ' . $id . ' at ' . now()->toDateTimeString());

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan');
        }

        // Hapus file gambar jika ada
        if ($user->image) {
            $imagePath = public_path('upload/users/' . $user->image);
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        // Hapus file legal authorization jika ada
        if ($user->legal_authorization_file) {
            $legal_authorization_filePath = public_path('upload/legal_authorizations/' . $user->legal_authorization_file);
            if (file_exists($legal_authorization_filePath)) {
                @unlink($legal_authorization_filePath);
            }
        }

        // Hapus file retirement decision letter jika ada
        if ($user->retirement_decision_letter) {
            $retirement_decision_letterPath = public_path('upload/retirement_letters/' . $user->retirement_decision_letter);
            if (file_exists($retirement_decision_letterPath)) {
                @unlink($retirement_decision_letterPath);
            }
        }

        // Hapus semua dokumen terkait user
        foreach ($user->documents as $document) {
            if ($document->document_file && file_exists(public_path('upload/documents/' . $document->document_file))) {
                @unlink(public_path('upload/documents/' . $document->document_file));
            }
            $document->delete();
        }

        // Hapus relasi role di pivot model_has_roles
        $user->roles()->detach();

        $oldData = $user->toArray();
        $user->delete();

        LogHelper::logAction('users', $id, 'Delete', $oldData, null);

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    public function verifyStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,nonactive,semi-active',
            'member_status' => 'required|in:active,nonactive,semi-active',
        ]);

        $user = User::findOrFail($request->user_id);
        $oldStatus = $user->status;

        $user->update(['status' => $request->status]);
        $user->update(['member_status' => $request->member_status]);

        LogHelper::logAction('users', $user->id, 'Update', ['old_status' => $oldStatus], ['new_status' => $user->status]);

        return redirect()->route('users.index')->with('success', 'Status user berhasil diperbarui.');
    }


    // In UserController.php
    public function updateRoles(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $oldRoles = $user->getRoleNames()->toArray();
        $user->syncRoles($request->input('role'));

        LogHelper::logAction(
            'users',
            $user->id,
            'Update',
            ['old_role' => $oldRoles ? $oldRoles[0] : null],
            ['new_role' => $request->input('role')]
        );

        return redirect()->route('users.index')
            ->with('success', 'Role user berhasil diperbarui');
    }


    public function editProfile(User $user): View
    {
        $title = "Halaman Edit Profil";
        $subtitle = "Menu Edit Profil";

        $roles = Role::pluck('name', 'name');
        $usersRole = $user->roles->pluck('name', 'name')->all();


        return view('user.edit', [
            'data_user' => $user,
            'roles' => $roles,
            'usersRole' => $usersRole,

            'title' => $title,
            'subtitle' => $subtitle
        ]);
    }
}
