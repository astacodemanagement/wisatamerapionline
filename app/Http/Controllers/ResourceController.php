<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
 
use Illuminate\Support\Facades\File;
 
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

class ResourceController extends Controller
{
 
    public function createForm(): View
    {
        $title = "Halaman Modul";
        $subtitle = "Menu Modul";
        return view('create_resource_form', compact('title', 'subtitle'));
    }

    public function createResource(Request $request)
    {
        $tableName = $request->input('nama_table');
        $fields = $request->input('fields');

        if (Schema::hasTable($tableName)) {
            return redirect()->back()->withErrors(['nama_table' => 'Tabel dengan nama tersebut sudah ada di database.'])->withInput();
        }

        $resourceName = Str::singular($tableName);
        $modelName = Str::studly($resourceName);
        $controllerName = "{$modelName}Controller";
        $controllerNamespace = "App\\Http\\Controllers\\{$controllerName}";
        $resourceRoute = "    Route::resource('{$resourceName}', {$controllerName}::class);\n";
        $useController = "use {$controllerNamespace};\n";

        // Create migration with explicit timestamp
        $timestamp = date('Y_m_d_His');
        $migrationName = "create_{$tableName}_table";
        $migrationClassName = "Create" . Str::studly($tableName) . "Table";

        // Create migration file content
        $migrationContent = "<?php\n\n";
        $migrationContent .= "use Illuminate\Database\Migrations\Migration;\n";
        $migrationContent .= "use Illuminate\Database\Schema\Blueprint;\n";
        $migrationContent .= "use Illuminate\Support\Facades\Schema;\n\n";
        $migrationContent .= "class {$migrationClassName} extends Migration\n{\n";
        $migrationContent .= "    public function up()\n    {\n";
        $migrationContent .= "        Schema::create('{$tableName}', function (Blueprint \$table) {\n";
        $migrationContent .= "            \$table->id();\n";

        foreach ($fields as $field) {
            $migrationContent .= "            \$table->{$field['type']}('{$field['name']}');\n";
        }

        $migrationContent .= "            \$table->timestamps();\n";
        $migrationContent .= "        });\n";
        $migrationContent .= "    }\n\n";
        $migrationContent .= "    public function down()\n    {\n";
        $migrationContent .= "        Schema::dropIfExists('{$tableName}');\n";
        $migrationContent .= "    }\n";
        $migrationContent .= "}\n";

        // Save migration file
        $migrationFileName = "{$timestamp}_{$migrationName}.php";
        $migrationPath = database_path("migrations/{$migrationFileName}");
        File::put($migrationPath, $migrationContent);

        // Run only this specific migration
        Artisan::call('migrate', [
            '--path' => "database/migrations/{$migrationFileName}"
        ]);

        // Create model
        Artisan::call('make:model', [
            'name' => $modelName
        ]);

        // Update model file
        $modelFile = app_path("Models/{$modelName}.php");
        if (File::exists($modelFile)) {
            $modelContent = File::get($modelFile);
            $modelContent = str_replace(
                'class ' . $modelName,
                "class {$modelName}\n{\n    protected \$table = '{$tableName}';\n    protected \$guarded = [];\n",
                $modelContent
            );
            File::put($modelFile, $modelContent);
        }

        // Create controller
        Artisan::call('make:controller', [
            'name' => $controllerName,
            '--resource' => true
        ]);

        // Create views directory and files
        $viewFolderPath = resource_path("views/{$resourceName}");
        if (!File::exists($viewFolderPath)) {
            File::makeDirectory($viewFolderPath, 0755, true);
        }

        $views = ['index', 'create', 'edit', 'show'];
        foreach ($views as $view) {
            File::put("$viewFolderPath/$view.blade.php", "<!-- Halaman $view untuk $modelName -->");
        }

        // Update web routes
        $webRouteFile = base_path('routes/web.php');
        if (File::exists($webRouteFile)) {
            $content = File::get($webRouteFile);
            if (strpos($content, $useController) === false) {
                $content = preg_replace("/(<\?php\n)/", "$1$useController", $content);
            }
            if (strpos($content, 'Route::group([\'middleware\' => [\'auth\']], function () {') !== false) {
                $content = preg_replace(
                    "/(Route::group\(\['middleware' => \['auth'\]\], function \(\) \{)/",
                    "$1\n$resourceRoute",
                    $content
                );
                File::put($webRouteFile, $content);
            }
        }

        // Tambahkan log history di sini
        $logData = [
            'table_name' => $tableName,
            'fields' => $fields,
            'model' => $modelName,
            'controller' => $controllerName,
            'migration' => $migrationName,
        ];
        LogHelper::logAction('dynamic_resources', null, 'Create', null, $logData);

        return redirect()->route('resource.create')
            ->with('success', "Resource untuk tabel {$tableName} berhasil dibuat dengan nama resource {$resourceName}.");
    }
}
