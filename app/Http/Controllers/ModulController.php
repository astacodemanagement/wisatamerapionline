<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Http;

class ModulController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:modul-list', ['only' => ['createForm']]);
        $this->middleware('permission:modul-create', ['only' => ['createResource']]);
    }

    public function createForm()
    {
        $title = "Modul";
        $subtitle = "Create of modul";
        return view('modul.index', compact('title', 'subtitle'));
    }

    public function createResource(Request $request)
    {
        // Validasi input
        $request->validate([
            'schema' => 'required|string',
        ]);

        $schema = $request->input('schema');

        // Parse nama tabel dari schema
        preg_match("/Schema::create\('(.*?)'/", $schema, $matches);
        if (!isset($matches[1])) {
            return redirect()->back()->withErrors(['schema' => 'Invalid schema format. Must contain Schema::create.'])->withInput();
        }
        $tableName = $matches[1];
        if (Schema::hasTable($tableName)) {
            return redirect()->back()->withErrors(['schema' => "Tabel '$tableName' sudah ada di database."])->withInput();
        }

        // Parse kolom dari schema
        $fields = [];
        preg_match_all("/\\\$table->([a-zA-Z]+)\('([^']+)'\)(.*?);/", $schema, $columnMatches, PREG_SET_ORDER);
        foreach ($columnMatches as $match) {
            $type = $match[1];
            $name = $match[2];
            $attributes = trim($match[3]);

            // Konversi tipe ke format yang sesuai untuk view dan validasi
            $fieldType = match ($type) {
                'text', 'longText' => 'text',
                'string', 'char', 'varchar' => 'string',
                'integer', 'bigInteger' => 'integer',
                'decimal', 'float' => 'decimal',
                'date', 'dateTime', 'timestamp' => 'date',
                'enum' => 'enum',
                default => 'string'
            };

            $fields[] = [
                'name' => $name,
                'type' => $fieldType,
                'attributes' => $attributes, // Simpan atribut seperti ->unique(), ->nullable(), dll.
            ];
        }

        // Validasi unik field names
        $fieldNames = array_column($fields, 'name');
        if (count($fieldNames) !== count(array_unique($fieldNames))) {
            return redirect()->back()->withErrors(['schema' => 'Duplikat nama kolom tidak diperbolehkan.'])->withInput();
        }

        // Setup nama resource
        $resourceName = Str::singular($tableName);
        $modelName = Str::studly($resourceName);
        $controllerName = "{$modelName}Controller";
        $controllerNamespace = "App\\Http\\Controllers\\{$controllerName}";
        $resourceRoute = "    Route::resource('{$tableName}', {$controllerName}::class);\n";
        $useController = "use {$controllerNamespace};\n";

        // Create migration
        $timestamp = date('Y_m_d_His');
        $migrationName = "create_{$tableName}_table";
        $migrationClassName = "Create" . Str::studly($tableName) . "Table";

        // Gunakan schema asli dari input
        $migrationContent = "<?php\n\n";
        $migrationContent .= "use Illuminate\\Database\\Migrations\\Migration;\n";
        $migrationContent .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
        $migrationContent .= "use Illuminate\\Support\\Facades\\Schema;\n\n";
        $migrationContent .= "class {$migrationClassName} extends Migration\n{\n";
        $migrationContent .= "    public function up()\n    {\n";
        $migrationContent .= "        " . $schema . "\n";
        $migrationContent .= "    }\n\n";
        $migrationContent .= "    public function down()\n    {\n";
        $migrationContent .= "        Schema::dropIfExists('$tableName');\n";
        $migrationContent .= "    }\n";
        $migrationContent .= "}\n";

        $migrationFileName = "{$timestamp}_{$migrationName}.php";
        $migrationPath = database_path("migrations/{$migrationFileName}");
        File::put($migrationPath, $migrationContent);

        // Jalankan migration
        Artisan::call('migrate', ['--path' => "database/migrations/{$migrationFileName}"]);

        // Create model
        $modelFile = app_path("Models/{$modelName}.php");
        $modelNamespace = "App\\Models";
        $modelContent = "<?php\n\n";
        $modelContent .= "namespace {$modelNamespace};\n\n";
        $modelContent .= "use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;\n";
        $modelContent .= "use Illuminate\\Database\\Eloquent\\Model;\n\n";
        $modelContent .= "class {$modelName} extends Model\n{\n";
        $modelContent .= "    use HasFactory;\n";
        $modelContent .= "    protected \$table = '{$tableName}';\n";
        // $modelContent .= "    protected \$fillable = ['" . implode("', '", array_column($fields, 'name')) . "'];\n";
        $modelContent .= "    protected \$guarded = [];\n";
        $modelContent .= "}\n";
        File::put($modelFile, $modelContent);

        // Clear cache
        Artisan::call('optimize:clear');
        Artisan::call('config:clear');

        // Create controller (sama seperti kode asli, tapi gunakan $fields dari parsing)
        // Create controller
        $controllerFile = app_path("Http/Controllers/{$controllerName}.php");
        $controllerContent = "<?php\n\n";
        $controllerContent .= "namespace App\\Http\\Controllers;\n\n";
        $controllerContent .= "use App\\Http\\Controllers\\Controller;\n";
        $controllerContent .= "use App\\Models\\{$modelName};\n";
        $controllerContent .= "use App\\Services\\ImageService;\n";
        $controllerContent .= "use Illuminate\\Http\\Request;\n";
        $controllerContent .= "use Illuminate\\Support\\Facades\\Log;\n";
        $controllerContent .= "use Illuminate\\Validation\\ValidationException;\n";
        $controllerContent .= "use Illuminate\\Support\\Facades\\DB;\n";
        $controllerContent .= "use App\\Helpers\\LogHelper;\n";
        $controllerContent .= "use Illuminate\\View\\View;\n\n";
        $controllerContent .= "class {$controllerName} extends Controller\n{\n";
        $controllerContent .= "    protected \$imageService;\n\n";
        $controllerContent .= "    public function __construct(ImageService \$imageService)\n    {\n";
        $controllerContent .= "        \$this->middleware('permission:" . Str::singular($tableName) . "-list', ['only' => ['index', 'show']]);\n";
        $controllerContent .= "        \$this->middleware('permission:" . Str::singular($tableName) . "-create', ['only' => ['store']]);\n";
        $controllerContent .= "        \$this->middleware('permission:" . Str::singular($tableName) . "-edit', ['only' => ['edit', 'update']]);\n";
        $controllerContent .= "        \$this->middleware('permission:" . Str::singular($tableName) . "-delete', ['only' => ['destroy']]);\n";
        $controllerContent .= "        \$this->imageService = \$imageService;\n";
        $controllerContent .= "    }\n\n";

        // Index method
        $controllerContent .= "    public function index(Request \$request): View\n    {\n";
        $controllerContent .= "        \$title = 'Halaman {$modelName}';\n";
        $controllerContent .= "        \$subtitle = 'Menu {$modelName}';\n";
        $controllerContent .= "        \${$tableName} = {$modelName}::all();\n";
        $controllerContent .= "        return view('{$tableName}.index', compact('{$tableName}', 'title', 'subtitle'));\n";
        $controllerContent .= "    }\n\n";

        // Store method
        $controllerContent .= "    public function store(Request \$request)\n    {\n";
        $controllerContent .= "        \$validated = \$request->validate([\n";
        foreach ($fields as $field) {
            $rules = [];
            if (strpos($field['attributes'], 'nullable()') === false) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }
            if ($field['type'] === 'string') {
                $rules[] = 'string';
                $rules[] = 'max:255';
                if (strpos($field['attributes'], 'unique()') !== false) {
                    $rules[] = "unique:{$tableName},{$field['name']}";
                }
            } elseif ($field['type'] === 'integer') {
                $rules[] = 'integer';
                if ($field['name'] === 'order_display') {
                    $rules[] = 'min:0';
                }
            } elseif ($field['type'] === 'decimal') {
                $rules[] = 'numeric';
            } elseif ($field['type'] === 'date') {
                $rules[] = 'date';
            } elseif ($field['type'] === 'text') {
                $rules[] = 'string';
            } elseif ($field['type'] === 'enum') {
                preg_match("/enum\('[^']+', \[(.*?)\]\)/", $field['attributes'], $enumMatches);
                if (isset($enumMatches[1])) {
                    $options = array_map('trim', explode(',', str_replace(['\'', '"'], '', $enumMatches[1])));
                    $rules[] = 'in:' . implode(',', $options);
                }
            }
            if ($field['name'] === 'image') {
                $rules = ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:4096'];
            }
            $controllerContent .= "            '{$field['name']}' => '" . implode('|', $rules) . "',\n";
        }
        $controllerContent .= "        ], [\n";
        foreach ($fields as $field) {
            if ($field['name'] === 'image') {
                $controllerContent .= "            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',\n";
                $controllerContent .= "            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',\n";
            } else {
                if (strpos($field['attributes'], 'nullable()') === false) {
                    $controllerContent .= "            '{$field['name']}.required' => '" . Str::studly($field['name']) . " wajib diisi.',\n";
                }
                if ($field['type'] === 'string') {
                    $controllerContent .= "            '{$field['name']}.max' => '" . Str::studly($field['name']) . " tidak boleh lebih dari 255 karakter.',\n";
                    if (strpos($field['attributes'], 'unique()') !== false) {
                        $controllerContent .= "            '{$field['name']}.unique' => '" . Str::studly($field['name']) . " sudah terdaftar.',\n";
                    }
                } elseif ($field['type'] === 'integer' && $field['name'] === 'order_display') {
                    $controllerContent .= "            '{$field['name']}.integer' => '" . Str::studly($field['name']) . " harus berupa angka.',\n";
                    $controllerContent .= "            '{$field['name']}.min' => '" . Str::studly($field['name']) . " tidak boleh kurang dari 0.',\n";
                } elseif ($field['type'] === 'enum') {
                    $controllerContent .= "            '{$field['name']}.in' => '" . Str::studly($field['name']) . " harus salah satu dari nilai yang diizinkan.',\n";
                }
            }
        }
        $controllerContent .= "        ]);\n\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            DB::beginTransaction();\n\n";
        $controllerContent .= "            \$input = \$validated;\n";
        $controllerContent .= "            if (\$request->hasFile('image')) {\n";
        $controllerContent .= "                \$input['image'] = \$this->imageService->handleImageUpload(\$request->file('image'), 'upload/{$tableName}');\n";
        $controllerContent .= "            }\n\n";
        $controllerContent .= "            \${$resourceName} = {$modelName}::create(\$input);\n\n";
        $controllerContent .= "            LogHelper::logAction('{$tableName}', \${$resourceName}->id, 'Create', null, \${$resourceName}->toArray());\n\n";
        $controllerContent .= "            DB::commit();\n\n";
        $controllerContent .= "            if (\$request->ajax()) {\n";
        $controllerContent .= "                return response()->json(['message' => '{$modelName} berhasil ditambahkan.', 'data' => \${$resourceName}], 200);\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return redirect()->route('{$tableName}.index')->with('success', '{$modelName} berhasil ditambahkan.');\n";
        $controllerContent .= "        } catch (ValidationException \$e) {\n";
        $controllerContent .= "            DB::rollBack();\n";
        $controllerContent .= "            if (\$request->ajax()) {\n";
        $controllerContent .= "                return response()->json(['message' => 'Validasi gagal.', 'errors' => \$e->errors()], 422);\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return redirect()->back()->withErrors(\$e->validator)->withInput();\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            DB::rollBack();\n";
        $controllerContent .= "            Log::error('Kesalahan saat menambahkan {$tableName}: ' . \$e->getMessage());\n";
        $controllerContent .= "            if (\$request->ajax()) {\n";
        $controllerContent .= "                return response()->json(['message' => 'Gagal menambahkan {$tableName}.', 'error' => \$e->getMessage()], 500);\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n\n";

        // Show method
        $controllerContent .= "    public function show(\$id)\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \${$resourceName} = {$modelName}::findOrFail(\$id);\n";
        $controllerContent .= "            return response()->json(\${$resourceName}, 200);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            Log::error('Gagal mengambil data {$tableName}: ' . \$e->getMessage());\n";
        $controllerContent .= "            return response()->json(['message' => 'Gagal mengambil data {$tableName}.', 'error' => \$e->getMessage()], 500);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n\n";

        // Edit method
        $controllerContent .= "    public function edit(\$id)\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \${$resourceName} = {$modelName}::findOrFail(\$id);\n";
        $controllerContent .= "            return response()->json(\${$resourceName}, 200);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            Log::error('Gagal mengambil data {$tableName} untuk edit: ' . \$e->getMessage());\n";
        $controllerContent .= "            return response()->json(['message' => 'Gagal mengambil data {$tableName}.', 'error' => \$e->getMessage()], 500);\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n\n";

        // Update method
        $controllerContent .= "    public function update(Request \$request, \$id)\n    {\n";
        $controllerContent .= "        \$validated = \$request->validate([\n";
        foreach ($fields as $field) {
            $rules = [];
            if (strpos($field['attributes'], 'nullable()') === false) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }
            if ($field['type'] === 'string') {
                $rules[] = 'string';
                $rules[] = 'max:255';
                if (strpos($field['attributes'], 'unique()') !== false) {
                    $rules[] = "unique:{$tableName},{$field['name']},\$id";
                }
            } elseif ($field['type'] === 'integer') {
                $rules[] = 'integer';
                if ($field['name'] === 'order_display') {
                    $rules[] = 'min:0';
                }
            } elseif ($field['type'] === 'decimal') {
                $rules[] = 'numeric';
            } elseif ($field['type'] === 'date') {
                $rules[] = 'date';
            } elseif ($field['type'] === 'text') {
                $rules[] = 'string';
            } elseif ($field['type'] === 'enum') {
                preg_match("/enum\('[^']+', \[(.*?)\]\)/", $field['attributes'], $enumMatches);
                if (isset($enumMatches[1])) {
                    $options = array_map('trim', explode(',', str_replace(['\'', '"'], '', $enumMatches[1])));
                    $rules[] = 'in:' . implode(',', $options);
                }
            }
            if ($field['name'] === 'image') {
                $rules = ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:4096'];
            }
            $controllerContent .= "            '{$field['name']}' => '" . implode('|', $rules) . "',\n";
        }
        $controllerContent .= "        ], [\n";
        foreach ($fields as $field) {
            if ($field['name'] === 'image') {
                $controllerContent .= "            'image.mimes' => 'Gambar harus berupa file dengan format JPG, JPEG, atau PNG.',\n";
                $controllerContent .= "            'image.max' => 'Ukuran gambar tidak boleh lebih dari 4MB.',\n";
            } else {
                if (strpos($field['attributes'], 'nullable()') === false) {
                    $controllerContent .= "            '{$field['name']}.required' => '" . Str::studly($field['name']) . " wajib diisi.',\n";
                }
                if ($field['type'] === 'string') {
                    $controllerContent .= "            '{$field['name']}.max' => '" . Str::studly($field['name']) . " tidak boleh lebih dari 255 karakter.',\n";
                    if (strpos($field['attributes'], 'unique()') !== false) {
                        $controllerContent .= "            '{$field['name']}.unique' => '" . Str::studly($field['name']) . " sudah terdaftar.',\n";
                    }
                } elseif ($field['type'] === 'integer' && $field['name'] === 'order_display') {
                    $controllerContent .= "            '{$field['name']}.integer' => '" . Str::studly($field['name']) . " harus berupa angka.',\n";
                    $controllerContent .= "            '{$field['name']}.min' => '" . Str::studly($field['name']) . " tidak boleh kurang dari 0.',\n";
                } elseif ($field['type'] === 'enum') {
                    $controllerContent .= "            '{$field['name']}.in' => '" . Str::studly($field['name']) . " harus salah satu dari nilai yang diizinkan.',\n";
                }
            }
        }
        $controllerContent .= "        ]);\n\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            DB::beginTransaction();\n\n";
        $controllerContent .= "            \${$resourceName} = {$modelName}::findOrFail(\$id);\n";
        $controllerContent .= "            \$oldData = \${$resourceName}->toArray();\n";
        $controllerContent .= "            \$input = \$validated;\n";
        $controllerContent .= "            if (\$request->hasFile('image')) {\n";
        $controllerContent .= "                \$input['image'] = \$this->imageService->handleImageUpload(\$request->file('image'), 'upload/{$tableName}', \${$resourceName}->image);\n";
        $controllerContent .= "            } else {\n";
        $controllerContent .= "                \$input['image'] = \${$resourceName}->image;\n";
        $controllerContent .= "            }\n\n";
        $controllerContent .= "            \${$resourceName}->update(\$input);\n\n";
        $controllerContent .= "            LogHelper::logAction('{$tableName}', \${$resourceName}->id, 'Update', \$oldData, \${$resourceName}->toArray());\n\n";
        $controllerContent .= "            DB::commit();\n\n";
        $controllerContent .= "            if (\$request->ajax()) {\n";
        $controllerContent .= "                return response()->json(['message' => '{$modelName} berhasil diperbarui.', 'data' => \${$resourceName}], 200);\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return redirect()->route('{$tableName}.index')->with('success', '{$modelName} berhasil diperbarui.');\n";
        $controllerContent .= "        } catch (ValidationException \$e) {\n";
        $controllerContent .= "            DB::rollBack();\n";
        $controllerContent .= "            Log::error('Validation error saat memperbarui {$tableName}: ' . json_encode(\$e->errors()));\n";
        $controllerContent .= "            if (\$request->ajax()) {\n";
        $controllerContent .= "                return response()->json(['message' => 'Validasi gagal.', 'errors' => \$e->errors()], 422);\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return redirect()->back()->withErrors(\$e->validator)->withInput();\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            DB::rollBack();\n";
        $controllerContent .= "            Log::error('Kesalahan saat memperbarui {$tableName}: ' . \$e->getMessage());\n";
        $controllerContent .= "            if (\$request->ajax()) {\n";
        $controllerContent .= "                return response()->json(['message' => 'Gagal memperbarui {$tableName}.', 'error' => \$e->getMessage()], 500);\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return redirect()->back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.')->withInput();\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n\n";

        // Destroy method
        $controllerContent .= "    public function destroy(\$id)\n    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            DB::beginTransaction();\n\n";
        $controllerContent .= "            \${$resourceName} = {$modelName}::findOrFail(\$id);\n";
        $controllerContent .= "            \$oldData = \${$resourceName}->toArray();\n";
        $controllerContent .= "            if (\${$resourceName}->image) {\n";
        $controllerContent .= "                \$filePath = public_path('upload/{$tableName}/' . \${$resourceName}->image);\n";
        $controllerContent .= "                if (file_exists(\$filePath)) {\n";
        $controllerContent .= "                    @unlink(\$filePath);\n";
        $controllerContent .= "                }\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            \${$resourceName}->delete();\n\n";
        $controllerContent .= "            LogHelper::logAction('{$tableName}', \${$resourceName}->id, 'Delete', \$oldData, null);\n\n";
        $controllerContent .= "            DB::commit();\n\n";
        $controllerContent .= "            if (request()->ajax()) {\n";
        $controllerContent .= "                return response()->json(['message' => '{$modelName} berhasil dihapus.'], 200);\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return redirect()->route('{$tableName}.index')->with('success', '{$modelName} berhasil dihapus.');\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            DB::rollBack();\n";
        $controllerContent .= "            Log::error('Gagal menghapus {$tableName}: ' . \$e->getMessage());\n";
        $controllerContent .= "            if (request()->ajax()) {\n";
        $controllerContent .= "                return response()->json(['message' => 'Gagal menghapus {$tableName}.', 'error' => \$e->getMessage()], 500);\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus {$tableName}.');\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "}\n";

        File::put($controllerFile, $controllerContent);




        // Create view folder and index.blade.php
        $viewFolderPath = resource_path("views/{$tableName}");
        if (!File::exists($viewFolderPath)) {
            File::makeDirectory($viewFolderPath, 0755, true);
        }

        $indexViewContent = "@extends('layouts.app')\n";
        $indexViewContent .= "@section('title', \$title)\n";
        $indexViewContent .= "@section('subtitle', \$subtitle)\n\n";
        $indexViewContent .= "@push('css')\n";
        $indexViewContent .= "    <link rel=\"stylesheet\" href=\"{{ asset('template/back/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}\">\n";
        $indexViewContent .= "    <style>\n";
        $indexViewContent .= "        .modal-dialog-scrollable .modal-body {\n";
        $indexViewContent .= "            max-height: 60vh;\n";
        $indexViewContent .= "            overflow-y: auto;\n";
        $indexViewContent .= "        }\n";
        $indexViewContent .= "        .modal-body { padding: 1.5rem; }\n";
        $indexViewContent .= "        .modal-footer { position: sticky; bottom: 0; z-index: 1; }\n";
        $indexViewContent .= "    </style>\n";
        $indexViewContent .= "@endpush\n\n";
        $indexViewContent .= "@section('content')\n";
        $indexViewContent .= "    <div class=\"container-fluid\">\n";
        $indexViewContent .= "        <div class=\"card bg-light-info shadow-none position-relative overflow-hidden\" style=\"border: solid 0.5px #ccc;\">\n";
        $indexViewContent .= "            <div class=\"card-body px-4 py-3\">\n";
        $indexViewContent .= "                <div class=\"row align-items-center\">\n";
        $indexViewContent .= "                    <div class=\"col-9\">\n";
        $indexViewContent .= "                        <h4 class=\"fw-semibold mb-8\">{{ \$title }}</h4>\n";
        $indexViewContent .= "                        <nav aria-label=\"breadcrumb\">\n";
        $indexViewContent .= "                            <ol class=\"breadcrumb\">\n";
        $indexViewContent .= "                                <li class=\"breadcrumb-item\"><a class=\"text-muted text-decoration-none\" href=\"/\">Beranda</a></li>\n";
        $indexViewContent .= "                                <li class=\"breadcrumb-item\" aria-current=\"page\">{{ \$subtitle }}</li>\n";
        $indexViewContent .= "                            </ol>\n";
        $indexViewContent .= "                        </nav>\n";
        $indexViewContent .= "                    </div>\n";
        $indexViewContent .= "                    <div class=\"col-3 text-center mb-n5\">\n";
        $indexViewContent .= "                        <img src=\"{{ asset('template/back/dist/images/breadcrumb/ChatBc.png') }}\" alt=\"\" class=\"img-fluid mb-n4\">\n";
        $indexViewContent .= "                    </div>\n";
        $indexViewContent .= "                </div>\n";
        $indexViewContent .= "            </div>\n";
        $indexViewContent .= "        </div>\n\n";
        $indexViewContent .= "        <section class=\"datatables\">\n";
        $indexViewContent .= "            <div class=\"row\">\n";
        $indexViewContent .= "                <div class=\"col-12\">\n";
        $indexViewContent .= "                    <div class=\"card\">\n";
        $indexViewContent .= "                        <div class=\"card-body\">\n";
        $indexViewContent .= "                            <div class=\"table-responsive\">\n";
        $indexViewContent .= "                                <div class=\"row\">\n";
        $indexViewContent .= "                                    <div class=\"col-lg-12 margin-tb\">\n";
        $indexViewContent .= "                                        @can('" . Str::singular($tableName) . "-create')\n";
        $indexViewContent .= "                                            <div class=\"pull-right\">\n";
        $indexViewContent .= "                                                <button type=\"button\" class=\"btn btn-primary mb-2\" data-bs-toggle=\"modal\" data-bs-target=\"#add{$modelName}Modal\">\n";
        $indexViewContent .= "                                                    <i class=\"fa fa-plus\"></i> Tambah Data\n";
        $indexViewContent .= "                                                </button>\n";
        $indexViewContent .= "                                            </div>\n";
        $indexViewContent .= "                                        @endcan\n";
        $indexViewContent .= "                                    </div>\n";
        $indexViewContent .= "                                </div>\n\n";
        $indexViewContent .= "                                @if (session('success'))\n";
        $indexViewContent .= "                                    <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n";
        $indexViewContent .= "                                        <strong>Berhasil!</strong> {{ session('success') }}\n";
        $indexViewContent .= "                                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>\n";
        $indexViewContent .= "                                    </div>\n";
        $indexViewContent .= "                                @endif\n\n";
        $indexViewContent .= "                                @if (session('error'))\n";
        $indexViewContent .= "                                    <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n";
        $indexViewContent .= "                                        <strong>Gagal!</strong> {{ session('error') }}\n";
        $indexViewContent .= "                                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>\n";
        $indexViewContent .= "                                    </div>\n";
        $indexViewContent .= "                                @endif\n\n";
        $indexViewContent .= "                                <table id=\"scroll_hor\" class=\"table border table-striped table-bordered display nowrap\" style=\"width: 100%\">\n";
        $indexViewContent .= "                                    <thead>\n";
        $indexViewContent .= "                                        <tr>\n";
        $indexViewContent .= "                                            <th width=\"5%\">No</th>\n";
        foreach ($fields as $field) {
            $indexViewContent .= "                                            <th>" . Str::studly($field['name']) . "</th>\n";
        }
        $indexViewContent .= "                                            <th width=\"280px\">Action</th>\n";
        $indexViewContent .= "                                        </tr>\n";
        $indexViewContent .= "                                    </thead>\n";
        $indexViewContent .= "                                    <tbody>\n";
        $indexViewContent .= "                                        @foreach (\${$tableName} as \$p)\n";
        $indexViewContent .= "                                            <tr>\n";
        $indexViewContent .= "                                                <td>{{ \$loop->iteration }}</td>\n";
        foreach ($fields as $field) {
            if ($field['name'] === 'image') {
                $indexViewContent .= "                                                <td>\n";
                $indexViewContent .= "                                                    @if (\$p->{$field['name']})\n";
                $indexViewContent .= "                                                        <a href=\"{{ asset('upload/{$tableName}/' . \$p->{$field['name']}) }}\" target=\"_blank\">Lihat Gambar</a>\n";
                $indexViewContent .= "                                                    @else\n";
                $indexViewContent .= "                                                        Tidak ada\n";
                $indexViewContent .= "                                                    @endif\n";
                $indexViewContent .= "                                                </td>\n";
            } elseif ($field['type'] === 'text') {
                $indexViewContent .= "                                                <td>{{ \$p->{$field['name']} ? Str::limit(\$p->{$field['name']}, 50) : 'N/A' }}</td>\n";
            } elseif ($field['type'] === 'enum' && $field['name'] === 'status') {
                $indexViewContent .= "                                                <td>{{ \$p->{$field['name']} === 'active' ? 'Aktif' : 'Tidak Aktif' }}</td>\n";
            } else {
                $indexViewContent .= "                                                <td>{{ \$p->{$field['name']} ?? 'Tidak ada' }}</td>\n";
            }
        }
        $indexViewContent .= "                                                <td>\n";
        $indexViewContent .= "                                                    <button class=\"btn btn-warning btn-sm btn-show-{$tableName}\" data-id=\"{{ \$p->id }}\">\n";
        $indexViewContent .= "                                                        <i class=\"fa fa-eye\"></i> Show\n";
        $indexViewContent .= "                                                    </button>\n";
        $indexViewContent .= "                                                    @can('" . Str::singular($tableName) . "-edit')\n";
        $indexViewContent .= "                                                        <button class=\"btn btn-success btn-sm btn-edit-{$tableName}\" data-id=\"{{ \$p->id }}\">\n";
        $indexViewContent .= "                                                            <i class=\"fa fa-edit\"></i> Edit\n";
        $indexViewContent .= "                                                        </button>\n";
        $indexViewContent .= "                                                    @endcan\n";
        $indexViewContent .= "                                                    @can('" . Str::singular($tableName) . "-delete')\n";
        $indexViewContent .= "                                                        <button type=\"button\" class=\"btn btn-danger btn-sm\" onclick=\"confirmDelete({{ \$p->id }})\">\n";
        $indexViewContent .= "                                                            <i class=\"fa fa-trash\"></i> Delete\n";
        $indexViewContent .= "                                                        </button>\n";
        $indexViewContent .= "                                                        <form id=\"delete-form-{{ \$p->id }}\" method=\"POST\" action=\"{{ route('{$tableName}.destroy', \$p->id) }}\" style=\"display:none;\">\n";
        $indexViewContent .= "                                                            @csrf\n";
        $indexViewContent .= "                                                            @method('DELETE')\n";
        $indexViewContent .= "                                                        </form>\n";
        $indexViewContent .= "                                                    @endcan\n";
        $indexViewContent .= "                                                </td>\n";
        $indexViewContent .= "                                            </tr>\n";
        $indexViewContent .= "                                        @endforeach\n";
        $indexViewContent .= "                                    </tbody>\n";
        $indexViewContent .= "                                </table>\n\n";
        $indexViewContent .= "                                <!-- Modal Tambah {$modelName} -->\n";
        $indexViewContent .= "                                <div class=\"modal fade\" id=\"add{$modelName}Modal\" tabindex=\"-1\" aria-labelledby=\"add{$modelName}ModalLabel\" aria-hidden=\"true\">\n";
        $indexViewContent .= "                                    <div class=\"modal-dialog modal-dialog-centered modal-dialog-scrollable\">\n";
        $indexViewContent .= "                                        <div class=\"modal-content border-0 shadow\">\n";
        $indexViewContent .= "                                            <form id=\"add-{$tableName}-form\" enctype=\"multipart/form-data\">\n";
        $indexViewContent .= "                                                @csrf\n";
        $indexViewContent .= "                                                <div class=\"modal-header bg-primary text-white\">\n";
        $indexViewContent .= "                                                    <h5 class=\"modal-title text-white\" id=\"add{$modelName}ModalLabel\">\n";
        $indexViewContent .= "                                                        <i class=\"bi bi-plus-circle me-2\"></i>Tambah {$modelName}\n";
        $indexViewContent .= "                                                    </h5>\n";
        $indexViewContent .= "                                                    <button type=\"button\" class=\"btn-close btn-close-white\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n";
        $indexViewContent .= "                                                </div>\n";
        $indexViewContent .= "                                                <div class=\"modal-body\">\n";
        $indexViewContent .= "                                                    <div class=\"row\">\n";
        $indexViewContent .= "                                                        <div class=\"col-md-12\">\n";
        foreach ($fields as $field) {
            $inputType = match ($field['type']) {
                'decimal', 'float' => 'number',
                'integer' => 'number',
                'date' => 'date',
                'text' => 'textarea',
                'enum' => 'select',
                default => 'text'
            };
            $required = strpos($field['attributes'], 'nullable()') === false ? 'required' : '';
            $label = Str::studly($field['name']);
            $indexViewContent .= "                                                            <div class=\"mb-3\">\n";
            $indexViewContent .= "                                                                <label for=\"{$tableName}-{$field['name']}\" class=\"form-label\">{$label}" . ($required ? ' <span class="text-danger">*</span>' : '') . "</label>\n";
            if ($inputType === 'textarea') {
                $indexViewContent .= "                                                                <textarea class=\"form-control\" id=\"{$tableName}-{$field['name']}\" name=\"{$field['name']}\" rows=\"4\" placeholder=\"Masukkan {$label}\" {$required}></textarea>\n";
            } elseif ($inputType === 'select') {
                $indexViewContent .= "                                                                <select class=\"form-control\" id=\"{$tableName}-{$field['name']}\" name=\"{$field['name']}\" {$required}>\n";
                preg_match("/enum\('[^']+', \[(.*?)\]\)/", $field['attributes'], $enumMatches);
                if (isset($enumMatches[1])) {
                    $options = array_map('trim', explode(',', str_replace(['\'', '"'], '', $enumMatches[1])));
                    foreach ($options as $option) {
                        $indexViewContent .= "                                                                    <option value=\"{$option}\">" . Str::studly($option) . "</option>\n";
                    }
                }
                $indexViewContent .= "                                                                </select>\n";
            } elseif ($field['name'] === 'image') {
                $indexViewContent .= "                                                                <input type=\"file\" class=\"form-control\" id=\"{$tableName}-{$field['name']}\" name=\"{$field['name']}\" accept=\".jpg,.jpeg,.png\" onchange=\"validate{$modelName}ImageUpload()\">\n";
            } else {
                $indexViewContent .= "                                                                <input type=\"{$inputType}\" class=\"form-control\" id=\"{$tableName}-{$field['name']}\" name=\"{$field['name']}\" placeholder=\"Masukkan {$label}\" {$required}>\n";
            }
            $indexViewContent .= "                                                                <div class=\"invalid-feedback\" id=\"{$tableName}-{$field['name']}-error\"></div>\n";
            if ($field['name'] === 'image') {
                $indexViewContent .= "                                                                <img id=\"{$tableName}-{$field['name']}-preview\" src=\"#\" alt=\"Gambar Preview\" style=\"display: none; max-width: 100%; margin-top: 10px;\">\n";
                $indexViewContent .= "                                                                <canvas id=\"{$tableName}-{$field['name']}-preview-canvas\" style=\"display: none; max-width: 100%; margin-top: 10px;\"></canvas>\n";
            }
            $indexViewContent .= "                                                            </div>\n";
        }
        $indexViewContent .= "                                                            <div id=\"{$tableName}-error-message\" class=\"text-danger small\"></div>\n";
        $indexViewContent .= "                                                        </div>\n";
        $indexViewContent .= "                                                    </div>\n";
        $indexViewContent .= "                                                </div>\n";
        $indexViewContent .= "                                                <div class=\"modal-footer bg-light\">\n";
        $indexViewContent .= "                                                    <button type=\"submit\" class=\"btn btn-primary\" id=\"btn-save\">\n";
        $indexViewContent .= "                                                        <i class=\"fa fa-save\"></i> Simpan\n";
        $indexViewContent .= "                                                    </button>\n";
        $indexViewContent .= "                                                    <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">\n";
        $indexViewContent .= "                                                        <i class=\"fa fa-undo\"></i> Batal\n";
        $indexViewContent .= "                                                    </button>\n";
        $indexViewContent .= "                                                </div>\n";
        $indexViewContent .= "                                            </form>\n";
        $indexViewContent .= "                                        </div>\n";
        $indexViewContent .= "                                    </div>\n";
        $indexViewContent .= "                                </div>\n\n";
        $indexViewContent .= "                                <!-- Modal Edit {$modelName} -->\n";
        $indexViewContent .= "                                <div class=\"modal fade\" id=\"edit{$modelName}Modal\" tabindex=\"-1\" aria-labelledby=\"edit{$modelName}ModalLabel\" aria-hidden=\"true\">\n";
        $indexViewContent .= "                                    <div class=\"modal-dialog modal-dialog-centered modal-dialog-scrollable\">\n";
        $indexViewContent .= "                                        <div class=\"modal-content border-0 shadow\">\n";
        $indexViewContent .= "                                            <form id=\"edit-{$tableName}-form\" enctype=\"multipart/form-data\">\n";
        $indexViewContent .= "                                                @csrf\n";
        $indexViewContent .= "                                                @method('PUT')\n";
        $indexViewContent .= "                                                <input type=\"hidden\" id=\"edit-{$tableName}-id\" name=\"id\" />\n";
        $indexViewContent .= "                                                <div class=\"modal-header bg-primary text-white\">\n";
        $indexViewContent .= "                                                    <h5 class=\"modal-title text-white\" id=\"edit{$modelName}ModalLabel\">\n";
        $indexViewContent .= "                                                        <i class=\"bi bi-pencil-square me-2\"></i>Edit {$modelName}\n";
        $indexViewContent .= "                                                    </h5>\n";
        $indexViewContent .= "                                                    <button type=\"button\" class=\"btn-close btn-close-white\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n";
        $indexViewContent .= "                                                </div>\n";
        $indexViewContent .= "                                                <div class=\"modal-body\">\n";
        $indexViewContent .= "                                                    <div id=\"edit-{$tableName}-error-message\" class=\"text-danger small mb-2\"></div>\n";
        $indexViewContent .= "                                                    <div class=\"row\">\n";
        $indexViewContent .= "                                                        <div class=\"col-md-12\">\n";
        foreach ($fields as $field) {
            $inputType = match ($field['type']) {
                'decimal', 'float' => 'number',
                'integer' => 'number',
                'date' => 'date',
                'text' => 'textarea',
                'enum' => 'select',
                default => 'text'
            };
            $required = strpos($field['attributes'], 'nullable()') === false ? 'required' : '';
            $label = Str::studly($field['name']);
            $indexViewContent .= "                                                            <div class=\"mb-3\">\n";
            $indexViewContent .= "                                                                <label for=\"edit-{$tableName}-{$field['name']}\" class=\"form-label\">{$label}" . ($required ? ' <span class="text-danger">*</span>' : '') . "</label>\n";
            if ($inputType === 'textarea') {
                $indexViewContent .= "                                                                <textarea class=\"form-control\" id=\"edit-{$tableName}-{$field['name']}\" name=\"{$field['name']}\" rows=\"4\" placeholder=\"Masukkan {$label}\" {$required}></textarea>\n";
            } elseif ($inputType === 'select') {
                $indexViewContent .= "                                                                <select class=\"form-control\" id=\"edit-{$tableName}-{$field['name']}\" name=\"{$field['name']}\" {$required}>\n";
                preg_match("/enum\('[^']+', \[(.*?)\]\)/", $field['attributes'], $enumMatches);
                if (isset($enumMatches[1])) {
                    $options = array_map('trim', explode(',', str_replace(['\'', '"'], '', $enumMatches[1])));
                    foreach ($options as $option) {
                        $indexViewContent .= "                                                                    <option value=\"{$option}\">" . Str::studly($option) . "</option>\n";
                    }
                }
                $indexViewContent .= "                                                                </select>\n";
            } elseif ($field['name'] === 'image') {
                $indexViewContent .= "                                                                <input type=\"file\" class=\"form-control\" id=\"edit-{$tableName}-{$field['name']}\" name=\"{$field['name']}\" accept=\".jpg,.jpeg,.png\" onchange=\"validateEdit{$modelName}ImageUpload()\">\n";
            } else {
                $indexViewContent .= "                                                                <input type=\"{$inputType}\" class=\"form-control\" id=\"edit-{$tableName}-{$field['name']}\" name=\"{$field['name']}\" placeholder=\"Masukkan {$label}\" {$required}>\n";
            }
            $indexViewContent .= "                                                                <div class=\"invalid-feedback\" id=\"edit-{$tableName}-{$field['name']}-error\"></div>\n";
            if ($field['name'] === 'image') {
                $indexViewContent .= "                                                                <img id=\"edit-{$tableName}-{$field['name']}-preview\" src=\"#\" alt=\"Gambar Preview\" style=\"display: none; max-width: 100%; margin-top: 10px;\">\n";
                $indexViewContent .= "                                                                <canvas id=\"edit-{$tableName}-{$field['name']}-preview-canvas\" style=\"display: none; max-width: 100%; margin-top: 10px;\"></canvas>\n";
            }
            $indexViewContent .= "                                                            </div>\n";
        }
        $indexViewContent .= "                                                        </div>\n";
        $indexViewContent .= "                                                    </div>\n";
        $indexViewContent .= "                                                </div>\n";
        $indexViewContent .= "                                                <div class=\"modal-footer bg-light\">\n";
        $indexViewContent .= "                                                    <button type=\"submit\" class=\"btn btn-primary\" id=\"btn-update\">\n";
        $indexViewContent .= "                                                        <i class=\"fa fa-save\"></i> Update\n";
        $indexViewContent .= "                                                    </button>\n";
        $indexViewContent .= "                                                    <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">\n";
        $indexViewContent .= "                                                        <i class=\"fa fa-undo\"></i> Batal\n";
        $indexViewContent .= "                                                    </button>\n";
        $indexViewContent .= "                                                </div>\n";
        $indexViewContent .= "                                            </form>\n";
        $indexViewContent .= "                                        </div>\n";
        $indexViewContent .= "                                    </div>\n";
        $indexViewContent .= "                                </div>\n\n";
        $indexViewContent .= "                                <!-- Modal Show {$modelName} -->\n";
        $indexViewContent .= "                                <div class=\"modal fade\" id=\"show{$modelName}Modal\" tabindex=\"-1\" aria-labelledby=\"show{$modelName}ModalLabel\" aria-hidden=\"true\">\n";
        $indexViewContent .= "                                    <div class=\"modal-dialog modal-dialog-centered modal-dialog-scrollable\">\n";
        $indexViewContent .= "                                        <div class=\"modal-content border-0 shadow\">\n";
        $indexViewContent .= "                                            <div class=\"modal-header bg-primary text-white\">\n";
        $indexViewContent .= "                                                <h5 class=\"modal-title text-white\" id=\"show{$modelName}ModalLabel\">\n";
        $indexViewContent .= "                                                    <i class=\"bi bi-eye me-2\"></i>Detail {$modelName}\n";
        $indexViewContent .= "                                                </h5>\n";
        $indexViewContent .= "                                                <button type=\"button\" class=\"btn-close btn-close-white\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n";
        $indexViewContent .= "                                            </div>\n";
        $indexViewContent .= "                                            <div class=\"modal-body\">\n";
        $indexViewContent .= "                                                <div id=\"show-{$tableName}-error-message\" class=\"text-danger small mb-2\"></div>\n";
        $indexViewContent .= "                                                <div class=\"row\">\n";
        $indexViewContent .= "                                                    <div class=\"col-md-12\">\n";
        foreach ($fields as $field) {
            $label = Str::studly($field['name']);
            $inputType = match ($field['type']) {
                'text' => 'textarea',
                default => 'text'
            };
            $indexViewContent .= "                                                        <div class=\"mb-3\">\n";
            $indexViewContent .= "                                                            <label for=\"show-{$tableName}-{$field['name']}\" class=\"form-label\">{$label}</label>\n";
            if ($inputType === 'textarea') {
                $indexViewContent .= "                                                            <textarea class=\"form-control\" id=\"show-{$tableName}-{$field['name']}\" rows=\"4\" readonly></textarea>\n";
            } elseif ($field['name'] === 'image') {
                $indexViewContent .= "                                                            <div id=\"show-{$tableName}-{$field['name']}\"></div>\n";
            } else {
                $indexViewContent .= "                                                            <input type=\"text\" class=\"form-control\" id=\"show-{$tableName}-{$field['name']}\" readonly>\n";
            }
            $indexViewContent .= "                                                        </div>\n";
        }
        $indexViewContent .= "                                                    </div>\n";
        $indexViewContent .= "                                                </div>\n";
        $indexViewContent .= "                                            </div>\n";
        $indexViewContent .= "                                            <div class=\"modal-footer bg-light\">\n";
        $indexViewContent .= "                                                <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">\n";
        $indexViewContent .= "                                                    <i class=\"fa fa-undo\"></i> Tutup\n";
        $indexViewContent .= "                                                </button>\n";
        $indexViewContent .= "                                            </div>\n";
        $indexViewContent .= "                                        </div>\n";
        $indexViewContent .= "                                    </div>\n";
        $indexViewContent .= "                                </div>\n";
        $indexViewContent .= "                            </div>\n";
        $indexViewContent .= "                        </div>\n";
        $indexViewContent .= "                    </div>\n";
        $indexViewContent .= "                </div>\n";
        $indexViewContent .= "            </div>\n";
        $indexViewContent .= "        </section>\n";
        $indexViewContent .= "    </div>\n";
        $indexViewContent .= "@endsection\n\n";
        $indexViewContent .= "@push('script')\n";
        $indexViewContent .= "    <script src=\"{{ asset('template/back/dist/libs/datatables.net/js/jquery.dataTables.min.js') }}\"></script>\n";
        $indexViewContent .= "    <script src=\"{{ asset('template/back/dist/js/datatable/datatable-basic.init.js') }}\"></script>\n";
        $indexViewContent .= "    <script>\n";
        $indexViewContent .= "        function validate{$modelName}ImageUpload() {\n";
        $indexViewContent .= "            const fileInput = document.getElementById('{$tableName}-image');\n";
        $indexViewContent .= "            const errorDiv = document.getElementById('{$tableName}-image-error');\n";
        $indexViewContent .= "            const previewImage = document.getElementById('{$tableName}-image-preview');\n";
        $indexViewContent .= "            const previewCanvas = document.getElementById('{$tableName}-image-preview-canvas');\n";
        $indexViewContent .= "            const file = fileInput.files[0];\n";
        $indexViewContent .= "            const maxSize = 4 * 1024 * 1024; // 4 MB dalam bytes\n";
        $indexViewContent .= "            const allowedTypes = ['image/jpeg', 'image/png'];\n\n";
        $indexViewContent .= "            errorDiv.style.display = 'none';\n";
        $indexViewContent .= "            errorDiv.textContent = '';\n";
        $indexViewContent .= "            previewImage.style.display = 'none';\n";
        $indexViewContent .= "            previewCanvas.style.display = 'none';\n";
        $indexViewContent .= "            fileInput.classList.remove('is-invalid');\n\n";
        $indexViewContent .= "            if (file) {\n";
        $indexViewContent .= "                if (!allowedTypes.includes(file.type)) {\n";
        $indexViewContent .= "                    errorDiv.textContent = 'File harus berupa JPEG atau PNG.';\n";
        $indexViewContent .= "                    errorDiv.style.display = 'block';\n";
        $indexViewContent .= "                    fileInput.classList.add('is-invalid');\n";
        $indexViewContent .= "                    fileInput.value = '';\n";
        $indexViewContent .= "                    return;\n";
        $indexViewContent .= "                }\n\n";
        $indexViewContent .= "                if (file.size > maxSize) {\n";
        $indexViewContent .= "                    errorDiv.textContent = 'Ukuran file terlalu besar. Maksimum 4 MB.';\n";
        $indexViewContent .= "                    errorDiv.style.display = 'block';\n";
        $indexViewContent .= "                    fileInput.classList.add('is-invalid');\n";
        $indexViewContent .= "                    fileInput.value = '';\n";
        $indexViewContent .= "                    return;\n";
        $indexViewContent .= "                }\n\n";
        $indexViewContent .= "                if (allowedTypes.includes(file.type)) {\n";
        $indexViewContent .= "                    const reader = new FileReader();\n";
        $indexViewContent .= "                    reader.onload = function(e) {\n";
        $indexViewContent .= "                        const img = new Image();\n";
        $indexViewContent .= "                        img.src = e.target.result;\n\n";
        $indexViewContent .= "                        img.onload = function() {\n";
        $indexViewContent .= "                            const canvasContext = previewCanvas.getContext('2d');\n";
        $indexViewContent .= "                            const maxWidth = 100;\n";
        $indexViewContent .= "                            const scaleFactor = maxWidth / img.width;\n";
        $indexViewContent .= "                            const newHeight = img.height * scaleFactor;\n\n";
        $indexViewContent .= "                            previewCanvas.width = maxWidth;\n";
        $indexViewContent .= "                            previewCanvas.height = newHeight;\n";
        $indexViewContent .= "                            canvasContext.drawImage(img, 0, 0, maxWidth, newHeight);\n\n";
        $indexViewContent .= "                            previewCanvas.style.display = 'block';\n";
        $indexViewContent .= "                            previewImage.style.display = 'none';\n";
        $indexViewContent .= "                        };\n";
        $indexViewContent .= "                    };\n";
        $indexViewContent .= "                    reader.readAsDataURL(file);\n";
        $indexViewContent .= "                }\n";
        $indexViewContent .= "            }\n";
        $indexViewContent .= "        }\n\n";
        $indexViewContent .= "        function validateEdit{$modelName}ImageUpload() {\n";
        $indexViewContent .= "            const fileInput = document.getElementById('edit-{$tableName}-image');\n";
        $indexViewContent .= "            const errorDiv = document.getElementById('edit-{$tableName}-image-error');\n";
        $indexViewContent .= "            const previewImage = document.getElementById('edit-{$tableName}-image-preview');\n";
        $indexViewContent .= "            const previewCanvas = document.getElementById('edit-{$tableName}-image-preview-canvas');\n";
        $indexViewContent .= "            const file = fileInput.files[0];\n";
        $indexViewContent .= "            const maxSize = 4 * 1024 * 1024;\n";
        $indexViewContent .= "            const allowedTypes = ['image/jpeg', 'image/png'];\n\n";
        $indexViewContent .= "            errorDiv.style.display = 'none';\n";
        $indexViewContent .= "            errorDiv.textContent = '';\n";
        $indexViewContent .= "            previewImage.style.display = 'none';\n";
        $indexViewContent .= "            previewCanvas.style.display = 'none';\n";
        $indexViewContent .= "            fileInput.classList.remove('is-invalid');\n\n";
        $indexViewContent .= "            if (file) {\n";
        $indexViewContent .= "                if (!allowedTypes.includes(file.type)) {\n";
        $indexViewContent .= "                    errorDiv.textContent = 'File harus berupa JPEG atau PNG.';\n";
        $indexViewContent .= "                    errorDiv.style.display = 'block';\n";
        $indexViewContent .= "                    fileInput.classList.add('is-invalid');\n";
        $indexViewContent .= "                    fileInput.value = '';\n";
        $indexViewContent .= "                    return;\n";
        $indexViewContent .= "                }\n\n";
        $indexViewContent .= "                if (file.size > maxSize) {\n";
        $indexViewContent .= "                    errorDiv.textContent = 'Ukuran file terlalu besar. Maksimum 4 MB.';\n";
        $indexViewContent .= "                    errorDiv.style.display = 'block';\n";
        $indexViewContent .= "                    fileInput.classList.add('is-invalid');\n";
        $indexViewContent .= "                    fileInput.value = '';\n";
        $indexViewContent .= "                    return;\n";
        $indexViewContent .= "                }\n\n";
        $indexViewContent .= "                if (allowedTypes.includes(file.type)) {\n";
        $indexViewContent .= "                    const reader = new FileReader();\n";
        $indexViewContent .= "                    reader.onload = function(e) {\n";
        $indexViewContent .= "                        const img = new Image();\n";
        $indexViewContent .= "                        img.src = e.target.result;\n\n";
        $indexViewContent .= "                        img.onload = function() {\n";
        $indexViewContent .= "                            const canvasContext = previewCanvas.getContext('2d');\n";
        $indexViewContent .= "                            const maxWidth = 100;\n";
        $indexViewContent .= "                            const scaleFactor = maxWidth / img.width;\n";
        $indexViewContent .= "                            const newHeight = img.height * scaleFactor;\n\n";
        $indexViewContent .= "                            previewCanvas.width = maxWidth;\n";
        $indexViewContent .= "                            previewCanvas.height = newHeight;\n";
        $indexViewContent .= "                            canvasContext.drawImage(img, 0, 0, maxWidth, newHeight);\n\n";
        $indexViewContent .= "                            previewCanvas.style.display = 'block';\n";
        $indexViewContent .= "                            previewImage.style.display = 'none';\n";
        $indexViewContent .= "                        };\n";
        $indexViewContent .= "                    };\n";
        $indexViewContent .= "                    reader.readAsDataURL(file);\n";
        $indexViewContent .= "                }\n";
        $indexViewContent .= "            }\n";
        $indexViewContent .= "        }\n\n";
        $indexViewContent .= "        $(document).ready(function() {\n";
        $indexViewContent .= "            function setButtonLoading(button, isLoading, loadingText = 'Menyimpan...') {\n";
        $indexViewContent .= "                if (!button || button.length === 0) return;\n";
        $indexViewContent .= "                if (isLoading) {\n";
        $indexViewContent .= "                    button.data('original-html', button.html());\n";
        $indexViewContent .= "                    button.prop('disabled', true).html('<span class=\"spinner-border spinner-border-sm\"></span> ' + loadingText);\n";
        $indexViewContent .= "                } else {\n";
        $indexViewContent .= "                    const original = button.data('original-html') || '<i class=\"fa fa-save\"></i> Simpan';\n";
        $indexViewContent .= "                    button.prop('disabled', false).html(original);\n";
        $indexViewContent .= "                }\n";
        $indexViewContent .= "            }\n\n";
        $indexViewContent .= "            function handleAjaxError(xhr, target = null) {\n";
        $indexViewContent .= "                let message = 'Terjadi kesalahan.';\n";
        $indexViewContent .= "                if (xhr.status === 422 && xhr.responseJSON?.errors) {\n";
        $indexViewContent .= "                    const errors = xhr.responseJSON.errors;\n";
        $indexViewContent .= "                    message = Object.values(errors).map(e => e[0]).join('<br>');\n";
        $indexViewContent .= "                    if (target) {\n";
        $indexViewContent .= "                        $(target).html(message);\n";
        $indexViewContent .= "                        $.each(errors, function(key, value) {\n";
        $indexViewContent .= "                            $('#' + target.replace('#', '') + '-' + key.replace('.', '-') + '-error').text(value[0]);\n";
        $indexViewContent .= "                            $('#' + target.replace('#', '') + '-' + key.replace('.', '-') ).addClass('is-invalid');\n";
        $indexViewContent .= "                        });\n";
        $indexViewContent .= "                    }\n";
        $indexViewContent .= "                } else if (xhr.status === 403) {\n";
        $indexViewContent .= "                    message = 'Anda tidak memiliki izin.';\n";
        $indexViewContent .= "                    if (target) $(target).html(message);\n";
        $indexViewContent .= "                } else if (xhr.responseJSON?.error) {\n";
        $indexViewContent .= "                    message = xhr.responseJSON.error;\n";
        $indexViewContent .= "                    if (target) $(target).html(message);\n";
        $indexViewContent .= "                }\n";
        $indexViewContent .= "                Swal.fire({\n";
        $indexViewContent .= "                    icon: 'error',\n";
        $indexViewContent .= "                    title: 'Error',\n";
        $indexViewContent .= "                    html: message,\n";
        $indexViewContent .= "                    confirmButtonText: 'OK'\n";
        $indexViewContent .= "                });\n";
        $indexViewContent .= "            }\n\n";
        $indexViewContent .= "            $('#add-{$tableName}-form').submit(function(e) {\n";
        $indexViewContent .= "                e.preventDefault();\n";
        $indexViewContent .= "                const form = $(this);\n";
        $indexViewContent .= "                const btn = $('#btn-save');\n";
        $indexViewContent .= "                const formData = new FormData(form[0]);\n\n";
        $indexViewContent .= "                setButtonLoading(btn, true);\n";
        $indexViewContent .= "                $('#{$tableName}-error-message').html('');\n";
        $indexViewContent .= "                $('.invalid-feedback').text('');\n";
        $indexViewContent .= "                $('.form-control').removeClass('is-invalid');\n\n";
        $indexViewContent .= "                $.ajax({\n";
        $indexViewContent .= "                    url: \"{{ route('{$tableName}.store') }}\",\n";
        $indexViewContent .= "                    type: 'POST',\n";
        $indexViewContent .= "                    data: formData,\n";
        $indexViewContent .= "                    processData: false,\n";
        $indexViewContent .= "                    contentType: false,\n";
        $indexViewContent .= "                    success: function(response) {\n";
        $indexViewContent .= "                        Swal.fire({\n";
        $indexViewContent .= "                            icon: 'success',\n";
        $indexViewContent .= "                            title: 'Berhasil!',\n";
        $indexViewContent .= "                            text: response.message\n";
        $indexViewContent .= "                        }).then(() => {\n";
        $indexViewContent .= "                            $('#add{$modelName}Modal').modal('hide');\n";
        $indexViewContent .= "                            form[0].reset();\n";
        $indexViewContent .= "                            $('#{$tableName}-image-preview').hide();\n";
        $indexViewContent .= "                            $('#{$tableName}-image-preview-canvas').hide();\n";
        $indexViewContent .= "                            location.reload();\n";
        $indexViewContent .= "                        });\n";
        $indexViewContent .= "                    },\n";
        $indexViewContent .= "                    error: function(xhr) {\n";
        $indexViewContent .= "                        handleAjaxError(xhr, '#{$tableName}-error-message');\n";
        $indexViewContent .= "                    },\n";
        $indexViewContent .= "                    complete: function() {\n";
        $indexViewContent .= "                        setButtonLoading(btn, false);\n";
        $indexViewContent .= "                    }\n";
        $indexViewContent .= "                });\n";
        $indexViewContent .= "            });\n\n";
        $indexViewContent .= "            $(document).on('click', '.btn-edit-{$tableName}', function() {\n";
        $indexViewContent .= "                const id = $(this).data('id');\n";
        $indexViewContent .= "                $('#edit-{$tableName}-error-message').html('');\n";
        $indexViewContent .= "                $('#edit-{$tableName}-form')[0].reset();\n";
        $indexViewContent .= "                $('.invalid-feedback').text('');\n";
        $indexViewContent .= "                $('.form-control').removeClass('is-invalid');\n";
        $indexViewContent .= "                $('#edit-{$tableName}-image-preview').hide();\n";
        $indexViewContent .= "                $('#edit-{$tableName}-image-preview-canvas').hide();\n\n";
        $indexViewContent .= "                $.ajax({\n";
        $indexViewContent .= "                    url: \"{{ route('{$tableName}.edit', ':id') }}\".replace(':id', id),\n";
        $indexViewContent .= "                    type: 'GET',\n";
        $indexViewContent .= "                    headers: { 'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content') },\n";
        $indexViewContent .= "                    success: function(response) {\n";
        $indexViewContent .= "                        if (response && response.id) {\n";
        $indexViewContent .= "                            $('#edit-{$tableName}-id').val(response.id);\n";
        foreach ($fields as $field) {
            if ($field['name'] === 'image') {
                // Bagian kode untuk menangani field image di dalam modal edit
                $indexViewContent .= "                            const imageUrl = response.{$field['name']} ? '{{ asset(\"upload/{$tableName}/\") }}' + response.{$field['name']} : null;\n";
                $indexViewContent .= "                            if (imageUrl && /\\.(jpg|jpeg|png|webp)\$/i.test(imageUrl)) {\n";
                $indexViewContent .= "                                $('#edit-{$tableName}-{$field['name']}-preview').attr('src', imageUrl).show();\n";
                $indexViewContent .= "                                $('#edit-{$tableName}-{$field['name']}-preview-canvas').hide();\n";
                $indexViewContent .= "                            } else {\n";
                $indexViewContent .= "                                $('#edit-{$tableName}-{$field['name']}-preview').hide();\n";
                $indexViewContent .= "                                $('#edit-{$tableName}-{$field['name']}-preview-canvas').hide();\n";
                $indexViewContent .= "                            }\n";
            } elseif ($field['type'] === 'enum' && $field['name'] === 'status') {
                $indexViewContent .= "                            $('#edit-{$tableName}-{$field['name']}').val(response.{$field['name']});\n";
            } else {
                $indexViewContent .= "                            $('#edit-{$tableName}-{$field['name']}').val(response.{$field['name']} || '');\n";
            }
        }
        $indexViewContent .= "                            $('#edit{$modelName}Modal').modal('show');\n";
        $indexViewContent .= "                        } else {\n";
        $indexViewContent .= "                            Swal.fire({ icon: 'error', title: 'Error', text: 'Data tidak ditemukan atau respons tidak valid.' });\n";
        $indexViewContent .= "                        }\n";
        $indexViewContent .= "                    },\n";
        $indexViewContent .= "                    error: function(xhr) {\n";
        $indexViewContent .= "                        handleAjaxError(xhr, '#edit-{$tableName}-error-message');\n";
        $indexViewContent .= "                    }\n";
        $indexViewContent .= "                });\n";
        $indexViewContent .= "            });\n\n";
        $indexViewContent .= "            $('#edit-{$tableName}-form').submit(function(e) {\n";
        $indexViewContent .= "                e.preventDefault();\n";
        $indexViewContent .= "                const form = $(this);\n";
        $indexViewContent .= "                const btn = $('#btn-update');\n";
        $indexViewContent .= "                const id = $('#edit-{$tableName}-id').val();\n";
        $indexViewContent .= "                const formData = new FormData(form[0]);\n";
        $indexViewContent .= "                formData.append('_method', 'PUT');\n\n";
        $indexViewContent .= "                setButtonLoading(btn, true, 'Memperbarui...');\n";
        $indexViewContent .= "                $('#edit-{$tableName}-error-message').html('');\n";
        $indexViewContent .= "                $('.invalid-feedback').text('');\n";
        $indexViewContent .= "                $('.form-control').removeClass('is-invalid');\n\n";
        $indexViewContent .= "                $.ajax({\n";
        $indexViewContent .= "                    url: \"{{ route('{$tableName}.update', ':id') }}\".replace(':id', id),\n";
        $indexViewContent .= "                    type: 'POST',\n";
        $indexViewContent .= "                    data: formData,\n";
        $indexViewContent .= "                    processData: false,\n";
        $indexViewContent .= "                    contentType: false,\n";
        $indexViewContent .= "                    success: function(response) {\n";
        $indexViewContent .= "                        Swal.fire({\n";
        $indexViewContent .= "                            icon: 'success',\n";
        $indexViewContent .= "                            title: 'Berhasil!',\n";
        $indexViewContent .= "                            text: response.message\n";
        $indexViewContent .= "                        }).then(() => {\n";
        $indexViewContent .= "                            $('#edit{$modelName}Modal').modal('hide');\n";
        $indexViewContent .= "                            location.reload();\n";
        $indexViewContent .= "                        });\n";
        $indexViewContent .= "                    },\n";
        $indexViewContent .= "                    error: function(xhr) {\n";
        $indexViewContent .= "                        handleAjaxError(xhr, '#edit-{$tableName}-error-message');\n";
        $indexViewContent .= "                    },\n";
        $indexViewContent .= "                    complete: function() {\n";
        $indexViewContent .= "                        setButtonLoading(btn, false, '<i class=\"fa fa-save\"></i> Update');\n";
        $indexViewContent .= "                    }\n";
        $indexViewContent .= "                });\n";
        $indexViewContent .= "            });\n\n";
        $indexViewContent .= "            $(document).on('click', '.btn-show-{$tableName}', function() {\n";
        $indexViewContent .= "                const id = $(this).data('id');\n";
        $indexViewContent .= "                $('#show-{$tableName}-error-message').html('');\n";
        $indexViewContent .= "                $.ajax({\n";
        $indexViewContent .= "                    url: \"{{ route('{$tableName}.show', ':id') }}\".replace(':id', id),\n";
        $indexViewContent .= "                    type: 'GET',\n";
        $indexViewContent .= "                    headers: { 'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content') },\n";
        $indexViewContent .= "                    success: function(response) {\n";
        $indexViewContent .= "                        if (response && response.id) {\n";
        foreach ($fields as $field) {
            if ($field['name'] === 'image') {
                // Bagian kode untuk menangani field image di dalam modal show
                $indexViewContent .= "                            const imageUrl = response.{$field['name']} ? '{{ asset(\"upload/{$tableName}/\") }}' + response.{$field['name']} : null;\n";
                $indexViewContent .= "                            if (imageUrl && /\\.(jpg|jpeg|png|webp)\$/i.test(imageUrl)) {\n";
                $indexViewContent .= "                                $('#show-{$tableName}-{$field['name']}').html('<a href=\"' + imageUrl + '\" target=\"_blank\"><img src=\"' + imageUrl + '\" class=\"img-fluid\" style=\"max-width: 50%;\" alt=\"Gambar {$label}\"></a>');\n";
                $indexViewContent .= "                            } else {\n";
                $indexViewContent .= "                                $('#show-{$tableName}-{$field['name']}').html('Tidak ada gambar');\n";
                $indexViewContent .= "                            }\n";
            } elseif ($field['type'] === 'enum' && $field['name'] === 'status') {
                $indexViewContent .= "                            $('#show-{$tableName}-{$field['name']}').val(response.{$field['name']} === 'active' ? 'Aktif' : 'Tidak Aktif');\n";
            } else {
                $indexViewContent .= "                            $('#show-{$tableName}-{$field['name']}').val(response.{$field['name']} || 'Tidak ada');\n";
            }
        }
        $indexViewContent .= "                            $('#show{$modelName}Modal').modal('show');\n";
        $indexViewContent .= "                        } else {\n";
        $indexViewContent .= "                            Swal.fire({ icon: 'error', title: 'Error', text: 'Data tidak ditemukan atau respons tidak valid.' });\n";
        $indexViewContent .= "                        }\n";
        $indexViewContent .= "                    },\n";
        $indexViewContent .= "                    error: function(xhr) {\n";
        $indexViewContent .= "                        handleAjaxError(xhr, '#show-{$tableName}-error-message');\n";
        $indexViewContent .= "                    }\n";
        $indexViewContent .= "                });\n";
        $indexViewContent .= "            });\n\n";
        $indexViewContent .= "            window.confirmDelete = function(id) {\n";
        $indexViewContent .= "                Swal.fire({\n";
        $indexViewContent .= "                    title: 'Apakah Anda yakin?',\n";
        $indexViewContent .= "                    text: 'Data yang dihapus tidak dapat dikembalikan!',\n";
        $indexViewContent .= "                    icon: 'warning',\n";
        $indexViewContent .= "                    showCancelButton: true,\n";
        $indexViewContent .= "                    confirmButtonColor: '#3085d6',\n";
        $indexViewContent .= "                    cancelButtonColor: '#d33',\n";
        $indexViewContent .= "                    confirmButtonText: 'Ya, hapus!',\n";
        $indexViewContent .= "                    cancelButtonText: 'Batal'\n";
        $indexViewContent .= "                }).then((result) => {\n";
        $indexViewContent .= "                    if (result.isConfirmed) {\n";
        $indexViewContent .= "                        $.ajax({\n";
        $indexViewContent .= "                            url: \"{{ route('{$tableName}.destroy', ':id') }}\".replace(':id', id),\n";
        $indexViewContent .= "                            type: 'DELETE',\n";
        $indexViewContent .= "                            data: { _token: '{{ csrf_token() }}' },\n";
        $indexViewContent .= "                            success: function(response) {\n";
        $indexViewContent .= "                                Swal.fire({\n";
        $indexViewContent .= "                                    icon: 'success',\n";
        $indexViewContent .= "                                    title: 'Berhasil!',\n";
        $indexViewContent .= "                                    text: response.message\n";
        $indexViewContent .= "                                }).then(() => {\n";
        $indexViewContent .= "                                    location.reload();\n";
        $indexViewContent .= "                                });\n";
        $indexViewContent .= "                            },\n";
        $indexViewContent .= "                            error: function(xhr) {\n";
        $indexViewContent .= "                                handleAjaxError(xhr);\n";
        $indexViewContent .= "                            }\n";
        $indexViewContent .= "                        });\n";
        $indexViewContent .= "                    }\n";
        $indexViewContent .= "                });\n";
        $indexViewContent .= "            };\n";
        $indexViewContent .= "        });\n";
        $indexViewContent .= "    </script>\n";
        $indexViewContent .= "@endpush\n";

        File::put("{$viewFolderPath}/index.blade.php", $indexViewContent);

        // Update routes/web.php
        $webRouteFile = base_path('routes/web.php');
        if (File::exists($webRouteFile)) {
            $content = File::get($webRouteFile);

            if (strpos($content, $useController) === false) {
                $content = preg_replace("/(<\?php\s+)/", "$1$useController", $content);
            }

            if (strpos($content, "Route::group(['middleware' => ['auth']], function () {") !== false) {
                $content = preg_replace(
                    "/(Route::group\(\['middleware' => \['auth'\]\], function \(\) \{)/",
                    "$1\n$resourceRoute",
                    $content
                );
            }

            File::put($webRouteFile, $content);
        }

        // Tambahkan izin Spatie
        $singularTableName = Str::singular($tableName);
        $permissions = [
            "{$singularTableName}-list",
            "{$singularTableName}-create",
            "{$singularTableName}-edit",
            "{$singularTableName}-delete",
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Log history setelah semua resource dibuat
        $fullModelClass = "{$modelNamespace}\\{$modelName}";
        if (class_exists($fullModelClass)) {
            $modelInstance = new $fullModelClass();
            $modelInstance->fill(['id' => 1]); // Dummy ID
            $newData = ['new_data' => "Pembuatan Modul ({$tableName})"];
            LogHelper::logAction($tableName, $modelInstance->id, 'Create', null, array_merge($modelInstance->toArray(), $newData));
        }

        return redirect()->route('modul.create')->with('success', "Resource untuk tabel {$tableName} berhasil dibuat.");
    }




    public function validateSchema(Request $request)
    {
        $request->validate([
            'schema' => 'required|string',
        ]);

        $schema = $request->input('schema');
        $apiKey = config('services.grok.api_key');

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post('https://api.x.ai/v1/chat/completions', [
                'model' => 'grok-4-latest',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert Laravel developer. Validate the provided Laravel migration schema and check if it is syntactically correct and usable. Return a JSON object with "valid" (boolean) and "message" (string) explaining the result.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Validate the following Laravel migration schema:\n$schema",
                    ],
                ],
                'stream' => false,
                'temperature' => 0.2,
                'max_tokens' => 300,
            ]);

            if ($response->successful()) {
                $result = $response->json()['choices'][0]['message']['content'] ?? '{}';
                $result = json_decode($result, true) ?: ['valid' => false, 'message' => 'Hasil validasi tidak tersedia.'];
                return response()->json([
                    'valid' => $result['valid'] ?? false,
                    'message' => $result['message'] ?? 'Hasil validasi tidak tersedia.'
                ], 200);
            }

            // Fallback jika API gagal
            $isValid = strpos($schema, 'Schema::create') !== false && strpos($schema, 'function (Blueprint $table)') !== false;
            return response()->json([
                'valid' => $isValid,
                'message' => $isValid ? 'Schema tampaknya valid.' : 'Schema tidak valid. Pastikan menggunakan Schema::create dan sintaks yang benar.'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error validating schema: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memvalidasi schema: ' . $e->getMessage()], 500);
        }
    }

    public function generateSchema(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $description = $request->input('description');
        $apiKey = config('services.grok.api_key');

        try {
            // Coba panggil API Grok
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post('https://api.x.ai/v1/chat/completions', [
                'model' => 'grok-4-latest',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert Laravel developer. Generate a valid Laravel migration schema using Schema::create based on the provided description. The schema must include an id and timestamps, and use the exact table name and columns mentioned in the description. Return only the schema code without additional explanation.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate a Laravel migration schema based on the following description: $description",
                    ],
                ],
                'stream' => false,
                'temperature' => 0.2,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $schema = $response->json()['choices'][0]['message']['content'] ?? null;
                if ($schema && strpos($schema, 'Schema::create') !== false) {
                    return response()->json(['schema' => $schema], 200);
                }
            }

            // Fallback jika API gagal atau schema tidak valid
            $tableName = $this->extractTableName($description);
            $columns = $this->extractColumns($description);

            $schema = "Schema::create('$tableName', function (Blueprint \$table) {\n";
            $schema .= "    \$table->id();\n";
            foreach ($columns as $column) {
                // Asumsikan kolom bertipe string jika tidak ada tipe spesifik
                $schema .= "    \$table->string('$column');\n";
            }
            $schema .= "    \$table->timestamps();\n";
            $schema .= "});";

            return response()->json(['schema' => $schema], 200);
        } catch (\Exception $e) {
            \Log::error('Error generating schema: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghasilkan schema: ' . $e->getMessage()], 500);
        }
    }

    private function extractTableName($description)
    {
        // Ekstrak nama tabel dari deskripsi
        preg_match('/(?:tabel|table)\s+(\w+)/i', $description, $matches);
        return Str::snake($matches[1] ?? 'table_name');
    }

    private function extractColumns($description)
    {
        // Ekstrak kolom dari deskripsi, misalnya "dengan kolom name, address, phone_number"
        preg_match('/(?:dengan kolom|with columns)\s+([\w,\s]+)/i', $description, $matches);
        if (isset($matches[1])) {
            // Pisahkan kolom berdasarkan koma dan bersihkan spasi
            $columns = array_map('trim', explode(',', $matches[1]));
            // Konversi ke snake_case dan pastikan valid
            return array_map(function ($column) {
                return Str::snake(preg_replace('/[^a-zA-Z0-9_]/', '', $column));
            }, $columns);
        }
        // Default kolom jika tidak ditemukan
        return ['name', 'description'];
    }
}
