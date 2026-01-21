<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\OtherSliderController;
use App\Http\Controllers\GalleryCategoryController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CountController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FaqServiceController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LogHistoryController;
use App\Http\Controllers\MenuGroupsController;
use App\Http\Controllers\MenuItemsController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PostingJobController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductSubCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PublicTaskController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\ReasonServiceController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\SubServiceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\HtmlMinifier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Artisan;

Route::get('/do-refresh', function () {
    // Hapus semua cache
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');

    // (Opsional) generate ulang cache baru biar lebih cepat setelah dibersihkan
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('optimize');

    return "<h3>✅ Semua cache Laravel berhasil dibersihkan dan diperbarui!</h3>
            <ul>
                <li>Cache dibersihkan</li>
                <li>Config dibersihkan dan dicache ulang</li>
                <li>Route dibersihkan dan dicache ulang</li>
                <li>View dibersihkan</li>
                <li>Optimize dijalankan</li>
            </ul>";
});




Route::middleware([HtmlMinifier::class])->group(function () {
    Route::get('/kelola', function () {
        return redirect()->route('login');
    });
    Auth::routes();
    Route::get('/', [FrontController::class, 'index'])->name('front.index');
    Route::get('/service', [FrontController::class, 'service'])->name('front.service');
    Route::get('/job', [FrontController::class, 'job'])->name('front.job');
    Route::get('/service/{id}', [FrontController::class, 'show_service'])->name('front.service.detail');
    Route::get('/reason', [FrontController::class, 'reason'])->name('front.reason');
    Route::get('/reason/{id}', [FrontController::class, 'show_reason'])->name('front.reason.detail');
    Route::get('/contact', [FrontController::class, 'contact'])->name('front.contact');
    Route::post('/contact', [FrontController::class, 'storeContact'])->name('front.contact.store');

    Route::get('/product', [FrontController::class, 'product'])->name('front.product');
    Route::get('/product/{slug}', [FrontController::class, 'productDetail'])->name('front.productDetail');


    // Route::get('/service/{slug}', [FrontController::class, 'serviceDetail'])->name('front.service.detail');
    // Route::get('/service/{service_slug}/{sub_service_slug}', [FrontController::class, 'subServiceDetail'])->name('front.sub_service.detail');

    Route::get('/blog', [FrontController::class, 'blog'])->name('front.blog');
    Route::get('/blog/{news_slug}', [FrontController::class, 'show_blog'])->name('front.blog.detail');

    Route::get('/destination', [FrontController::class, 'destinationIndex'])->name('front.destinations');
    Route::get('/destination/{slug}', [FrontController::class, 'destinationDetail'])->name('destination.detail');

    Route::get('/gallery', [FrontController::class, 'galleryIndex'])->name('front.gallery');

    Route::get('/tour', [FrontController::class, 'tourIndex'])->name('tour.index');
    Route::get('/tour/{slug}', [FrontController::class, 'tourDetail'])->name('tour.detail');



    Route::post('/newsletter/subscribe', [FrontController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::get('/about', [FrontController::class, 'about'])->name('front.about');
    Route::get('/team', [FrontController::class, 'team'])->name('front.team');

    // Route::get('/daftar', [DaftarController::class, 'index'])->name('daftar');
    Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);


    Route::group(['middleware' => ['auth']], function () {
        Route::resource('job_vacancies', JobVacancyController::class);

        Route::resource('other_sliders', OtherSliderController::class);

        Route::resource('gallery_categories', GalleryCategoryController::class);

        Route::resource('galleries', GalleryController::class);

        Route::resource('counts', CountController::class);

        Route::resource('tours', TourController::class);
        Route::resource('agents', AgentController::class);

        Route::resource('destinations', DestinationController::class);

        Route::resource('newsletters', NewsletterController::class);

        Route::resource('sliders', SliderController::class);

        Route::resource('tasks', TaskController::class);
        Route::resource('divisions', DivisionController::class);
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::resource('services', ServiceController::class);
        Route::resource('subservices', SubServiceController::class);
        Route::resource('portfolios', PortfolioController::class);
        Route::resource('designs', DesignController::class);
        Route::resource('plannings', PlanningController::class);
        Route::resource('faq-services', FaqServiceController::class);
        Route::resource('reason-services', ReasonServiceController::class);
        Route::resource('reasons', ReasonController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('testimonials', TestimonialController::class);
        Route::resource('project-categories', ProjectCategoryController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('faqs', FaqController::class);
        Route::resource('blog-categories', BlogCategoryController::class);
        Route::resource('blogs', BlogController::class);
        Route::resource('teams', TeamController::class);
        Route::resource('legals', LegalController::class);
        Route::resource('privacy-policies', PrivacyPolicyController::class);
        Route::resource('posting-jobs', PostingJobController::class);
        Route::resource('units', UnitController::class);
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::resource('product-categories', ProductCategoryController::class);
        Route::resource('product-sub-categories', ProductSubCategoryController::class);
        Route::resource('products', ProductController::class);
        // Rute tambahan untuk mengelola gambar tambahan
        Route::get('products/{id}/images', [ProductController::class, 'manageImages'])->name('products.manageImages');
        Route::post('products/{id}/images', [ProductController::class, 'storeAdditionalImages'])->name('products.storeAdditionalImages');
        Route::delete('products/{id}/images/{imageId}', [ProductController::class, 'deleteAdditionalImage'])->name('products.deleteAdditionalImage');



        Route::get('/tutorial-status', [TutorialController::class, 'getTutorialStatus']);
        Route::post('/set-tutorial-status', [TutorialController::class, 'setTutorialStatus']);
        Route::resource('routes', RouteController::class);
        Route::get('/generate-routes', [RouteController::class, 'generateRoutes'])->name('routes.generate');
        Route::resource('log_histori', LogHistoryController::class);
        Route::get('/log-histori/delete-all', [LogHistoryController::class, 'deleteAll'])->name('log-histori.delete-all');
        Route::get('/logs', [LogHistoryController::class, 'show'])->name('logs.show');
        Route::post('/logs/clear', [LogHistoryController::class, 'clear'])->name('logs.clear');
        Route::resource('roles', RolesController::class);
        Route::resource('users', UsersController::class);
        Route::get('users/profile/{user:uuid}/edit', [UsersController::class, 'editProfile'])->name('users.profile.edit');
        Route::delete('user-documents/{id}', [UsersController::class, 'destroyDocument'])->name('user-documents.destroy');
        Route::post('/users/verify-status', [UsersController::class, 'verifyStatus'])->name('users.verifyStatus');
        Route::patch('users/{id}/roles', [UsersController::class, 'updateRoles'])->name('users.updateRoles');
        Route::resource('permissions', PermissionsController::class);
        Route::put('/profil/update_setting/{id}', [ProfilController::class, 'update_setting'])->name('profil.update_setting');
        Route::resource('menu_groups', MenuGroupsController::class);
        Route::resource('menu_items', MenuItemsController::class);
        Route::post('menu-items/update-positions', [MenuItemsController::class, 'updatePositions'])->name('menu_items.update_positions');
        Route::post('menu-groups/update-positions', [MenuGroupsController::class, 'updatePositions'])->name('menu_groups.update_positions');
        Route::get('/create-resource', [ResourceController::class, 'createForm'])->name('resource.create');
        Route::post('/create-resource', [ResourceController::class, 'createResource'])->name('resource.store');
        Route::resource('/backupdatabase', BackupController::class);
        Route::get('/backup/manual', [BackupController::class, 'manualBackup'])->name('backup.manual');
        Route::resource('profil', ProfilController::class);
        Route::get('/modul', [ModulController::class, 'createForm'])->name('modul.create');
        Route::post('/modul', [ModulController::class, 'createResource'])->name('modul.store');

        Route::post('modul/generate-schema', [ModulController::class, 'generateSchema'])->name('modul.generate-schema');
        Route::post('modul/validate-schema', [ModulController::class, 'validateSchema'])->name('modul.validate-schema');
    });
});
