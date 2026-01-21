<?php

namespace App\Http\Controllers;

use App\Models\LogHistori;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:profil-list|profil-create|profil-edit|profil-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:profil-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:profil-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:profil-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $title = "Halaman Profil";
        $subtitle = "Menu Profil";
        $data_profil = Profil::all();

        return view('profil.index', compact('data_profil', 'title', 'subtitle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('profil.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);

        Profil::create($request->all());

        return redirect()->route('profil.index')
            ->with('success', 'Profil created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Profil  $profil
     * @return \Illuminate\Http\Response
     */
    public function show(Profil $profil): View
    {
        return view('profil.show', compact('profil'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Profil  $profil
     * @return \Illuminate\Http\Response
     */
    public function edit(): View
    {
        $profil = Profil::findOrFail(1);
        $title = "Halaman Profil";
        $subtitle = "Menu Profil";
        return view('profil.edit', compact('profil', 'title', 'subtitle'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profil  $profil
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'nama_profil' => 'required',
            'no_telp' => 'required|numeric',
            'no_wa' => 'required|numeric',
            'email' => 'required|email',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'logo_dark' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'favicon' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'banner' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'gambar_1' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'gambar_2' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'gambar_3' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'gambar_4' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'breadcrumb_1' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'breadcrumb_2' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'breadcrumb_3' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'breadcrumb_4' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'breadcrumb_5' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'breadcrumb_6' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
            'ttd' => 'image|mimes:jpeg,png,jpg,gif|max:6048',
        ], [
            'nama_profil.required' => 'Nama profil wajib diisi.',
            'no_telp.required' => 'No Telp wajib diisi.',
            'no_telp.numeric' => 'No Telp harus berupa angka.',
            'no_wa.required' => 'No WA wajib diisi.',
            'no_wa.numeric' => 'No WA harus berupa angka.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nama_profil.unique' => 'Nama profil sudah ada.',
            'logo.image' => 'Gambar harus dalam format jpeg, jpg, atau png',
            'logo.mimes' => 'Format logo harus jpeg, jpg, atau png',
            'logo.max' => 'Ukuran logo tidak boleh lebih dari 6 MB',
            'favicon.image' => 'Favicon harus dalam format jpeg, jpg, atau png',
            'favicon.mimes' => 'Format favicon harus jpeg, jpg, atau png',
            'favicon.max' => 'Ukuran favicon tidak boleh lebih dari 6 MB',
            'banner.image' => 'Favicon harus dalam format jpeg, jpg, atau png',
            'banner.mimes' => 'Format banner harus jpeg, jpg, atau png',
            'banner.max' => 'Ukuran banner tidak boleh lebih dari 6 MB',
            'logo_dark.image' => 'Favicon harus dalam format jpeg, jpg, atau png',
            'logo_dark.mimes' => 'Format logo dark harus jpeg, jpg, atau png',
            'logo_dark.max' => 'Ukuran logo dark tidak boleh lebih dari 6 MB',
            'ttd.image' => 'TTD harus dalam format jpeg, jpg, atau png',
            'ttd.mimes' => 'Format logo dark harus jpeg, jpg, atau png',
            'ttd.max' => 'Ukuran logo dark tidak boleh lebih dari 6 MB',
            
             'gambar_1.image' => 'Favicon harus dalam format jpeg, jpg, atau png',
            'gambar_1.mimes' => 'Format Gambar 1 harus jpeg, jpg, atau png',
            'gambar_1.max' => 'Ukuran Gambar 1 tidak boleh lebih dari 6 MB',
            
               'gambar_2.image' => 'Favicon harus dalam format jpeg, jpg, atau png',
            'gambar_2.mimes' => 'Format Gambar 2 harus jpeg, jpg, atau png',
            'gambar_2.max' => 'Ukuran Gambar 2 tidak boleh lebih dari 6 MB',
            
            
               'gambar_3.image' => 'Favicon harus dalam format jpeg, jpg, atau png',
            'gambar_3.mimes' => 'Format Gambar 3 harus jpeg, jpg, atau png',
            'gambar_3.max' => 'Ukuran Gambar 3 tidak boleh lebih dari 6 MB',
            
            
               'gambar_4.image' => 'Favicon harus dalam format jpeg, jpg, atau png',
            'gambar_4.mimes' => 'Format Gambar 4 harus jpeg, jpg, atau png',
            'gambar_4.max' => 'Ukuran Gambar 4 tidak boleh lebih dari 6 MB',

            'breadcrumb_1.image' => 'Gambar harus dalam format jpeg, jpg, atau png',
            'breadcrumb_1.mimes' => 'Format Breadcrumb 1 harus jpeg, jpg, atau png',
            'breadcrumb_1.max' => 'Ukuran Breadcrumb 1 tidak boleh lebih dari 6 MB',

            'breadcrumb_2.image' => 'Gambar harus dalam format jpeg, jpg, atau png',
            'breadcrumb_2.mimes' => 'Format Breadcrumb 2 harus jpeg, jpg, atau png',
            'breadcrumb_2.max' => 'Ukuran Breadcrumb 2 tidak boleh lebih dari 6 MB',

            'breadcrumb_3.image' => 'Gambar harus dalam format jpeg, jpg, atau png',
            'breadcrumb_3.mimes' => 'Format Breadcrumb 3 harus jpeg, jpg, atau png',
            'breadcrumb_3.max' => 'Ukuran Breadcrumb 3 tidak boleh lebih dari 6 MB',

            'breadcrumb_4.image' => 'Gambar harus dalam format jpeg, jpg, atau png',
            'breadcrumb_4.mimes' => 'Format Breadcrumb 4 harus jpeg, jpg, atau png',
            'breadcrumb_4.max' => 'Ukuran Breadcrumb 4 tidak boleh lebih dari 6 MB',

            'breadcrumb_5.image' => 'Gambar harus dalam format jpeg, jpg, atau png',
            'breadcrumb_5.mimes' => 'Format Breadcrumb 5 harus jpeg, jpg, atau png',
            'breadcrumb_5.max' => 'Ukuran Breadcrumb 5 tidak boleh lebih dari 6 MB',

            'breadcrumb_6.image' => 'Gambar harus dalam format jpeg, jpg, atau png',
            'breadcrumb_6.mimes' => 'Format Breadcrumb 6 harus jpeg, jpg, atau png',
            'breadcrumb_6.max' => 'Ukuran Breadcrumb 6 tidak boleh lebih dari 6 MB',
        ]);

        $profil = Profil::find($id);
        $oldData = $profil->toArray();
        $input = $request->all();

        // Handle Image Deletions
        $imageFields = [
            'logo', 'logo_dark', 'favicon', 'banner', 'bg_login', 'ttd',
            'breadcrumb_1', 'breadcrumb_2', 'breadcrumb_3', 'breadcrumb_4', 'breadcrumb_5', 'breadcrumb_6',
            'gambar_1', 'gambar_2', 'gambar_3', 'gambar_4'
        ];

        foreach ($imageFields as $field) {
            if ($request->get('delete_' . $field) == '1') {
                $oldPictureFileName = $profil->$field;
                if ($oldPictureFileName) {
                    $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $input[$field] = null;
            }
             unset($input['delete_' . $field]);
        }

        if ($request->hasFile('ttd')) {
            $oldPictureFileName = $profil->ttd;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('ttd');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';

                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;
                    default:
                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);

                    @unlink($sourceImagePath);

                    $input['ttd'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }


        if ($request->hasFile('logo')) {
            $oldPictureFileName = $profil->logo;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('logo');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';

                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;
                    default:
                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);

                    @unlink($sourceImagePath);

                    $input['logo'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }


        if ($request->hasFile('logo_dark')) {
            $oldPictureFileName = $profil->logo_dark;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('logo_dark');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';

                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;
                    default:
                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);

                    @unlink($sourceImagePath);

                    $input['logo_dark'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }


        if ($request->hasFile('favicon')) {
            $oldPictureFileName = $profil->favicon;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('favicon');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;

                    default:

                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);

                    @unlink($sourceImagePath);

                    $input['favicon'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }


        if ($request->hasFile('banner')) {
            $oldPictureFileName = $profil->banner;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('banner');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';

                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;
                    default:
                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);
                    @unlink($sourceImagePath);
                    $input['banner'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }

        if ($request->hasFile('bg_login')) {
            $oldPictureFileName = $profil->bg_login;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('bg_login');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();
            $imageMimeType = $image->getMimeType();
            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);
                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);
                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;
                    default:
                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);
                    @unlink($sourceImagePath);
                    $input['bg_login'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }
        
        
        

 if ($request->hasFile('gambar_1')) {
            $oldPictureFileName = $profil->gambar_1;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('gambar_1');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;

                    default:

                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);

                    @unlink($sourceImagePath);

                    $input['gambar_1'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }


         if ($request->hasFile('gambar_2')) {
            $oldPictureFileName = $profil->gambar_2;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath)
                    ;
                }
                
            }

            $image = $request->file('gambar_2');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;

                    default:

                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);

                    @unlink($sourceImagePath);

                    $input['gambar_2'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }



         if ($request->hasFile('gambar_3')) {
            $oldPictureFileName = $profil->gambar_3;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('gambar_3');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;

                    default:

                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);

                    @unlink($sourceImagePath);

                    $input['gambar_3'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }

        


         if ($request->hasFile('gambar_4')) {
            $oldPictureFileName = $profil->gambar_4;
            if ($oldPictureFileName) {
                $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $image = $request->file('gambar_4');
            $destinationPath = 'upload/profil/';

            $originalFileName = $image->getClientOriginalName();

            $imageMimeType = $image->getMimeType();

            if (strpos($imageMimeType, 'image/') === 0) {
                $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

                $image->move($destinationPath, $imageName);

                $sourceImagePath = public_path($destinationPath . $imageName);

                $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                switch ($imageMimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourceImagePath);
                        break;

                    default:

                        break;
                }

                if ($sourceImage !== false) {
                    imagewebp($sourceImage, $webpImagePath);

                    imagedestroy($sourceImage);

                    @unlink($sourceImagePath);

                    $input['gambar_4'] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                } else {
                }
            } else {
            }
        }


        for ($i = 1; $i <= 6; $i++) {
            $fieldName = 'breadcrumb_' . $i;
            if ($request->hasFile($fieldName)) {
                $oldPictureFileName = $profil->$fieldName;
                if ($oldPictureFileName) {
                    $oldFilePath = public_path('upload/profil/' . $oldPictureFileName);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $image = $request->file($fieldName);
                $destinationPath = 'upload/profil/';
                $originalFileName = $image->getClientOriginalName();
                $imageMimeType = $image->getMimeType();

                if (strpos($imageMimeType, 'image/') === 0) {
                    $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);
                    $image->move($destinationPath, $imageName);
                    $sourceImagePath = public_path($destinationPath . $imageName);
                    $webpImagePath = $destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp';

                    switch ($imageMimeType) {
                        case 'image/jpeg':
                            $sourceImage = @imagecreatefromjpeg($sourceImagePath);
                            break;
                        case 'image/png':
                            $sourceImage = @imagecreatefrompng($sourceImagePath);
                            break;
                        default:
                            $sourceImage = false;
                            break;
                    }

                    if ($sourceImage !== false) {
                        imagewebp($sourceImage, $webpImagePath);
                        imagedestroy($sourceImage);
                        @unlink($sourceImagePath);
                        $input[$fieldName] = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
                    }
                }
            }
        }

        $profil->update($input);

        LogHelper::logAction(
            'profil',
            $profil->id,
            'Update',
            $oldData,
            $profil->toArray()
        );

        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    public function update_setting(Request $request, $id)
    {
        try {
            $profil = Profil::findOrFail($id);

            $input = $request->all();


            $oldData = $profil->toArray();

            LogHelper::logAction(
                'profil',
                $profil->id,
                'Update',
                $oldData,
                $profil->toArray()
            );

            $profil->update($input);
            return redirect()->back()
                ->with('success', 'Data berhasil diupdate');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        }
    }

 

     
}
