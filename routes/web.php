<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Livewire\Admin\AdminUsersPanel;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\DocuPostPanel;
use App\Livewire\Boomarks;
use App\Livewire\DocuSearchResult;
use App\Livewire\Home;
use App\Livewire\ProfileEditTab;
use App\Livewire\EditProfile;
use App\Http\Livewire\Admin\{AddNewUser, CreateUsers};
use App\Livewire\UploadDocument;
use App\Livewire\User\AdvancedSearchResults;
use App\Livewire\ViewDocuPost;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('index');
})->name('index');


Route::get('/skeleton', function () {
    return view('livewire.placeholder.setting-skeleton');
});

// Route::controller(UserController:: class)->group(funtion(){
//     Route::post('/create', 'create')->name('user.create');
// Route::post('/logout', 'logout')->name('user.logout');
// Route::post('/login/process', 'loginProcess')->name('login-process');
// });
Route::prefix('help')->group(function () {
    Route::get('/main', function () {
        return view('pages.user.help-and-support-user');
    })->name('help-and-support');

    Route::get('/account-management', function () {
        return view('pages.user.help-and-support-pages.account-help-support');
    })->name('account-help-center');

    Route::get('/using-theses-kiosk', function () {
        return view('pages.user.help-and-support-pages.using-theses-kiosk-help-support');
    })->name('using-theses-kiosk-help-center');

    Route::get('/reporting', function () {
        return view('pages.user.help-and-support-pages.reporting-help-support');
    })->name('reporting-help-center');


});
Route::post('/create', [UserController::class, 'create'])->name('user.create');
Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');

Route::post('/login/process', [UserController::class, 'loginProcess'])->name('login-process');
Route::get('/profile/{username?}', [UserController::class, 'viewUser'])->name('user-profile');

// only autehnticated user
Route::middleware(['auth', 'user'])->group(function () {


    Route::get('/tabtester', ProfileEditTab::class);

    Route::get('profile/edit/{activeTab?}', EditProfile::class)->name('edit-profile');
    Route::get('/upload-document', UploadDocument::class)->name('user-upload-document-form');
    Route::get('/view-pdf/{title}/{pdfFile}/{docuPostID}', function ($title, $pdfFile, $docuPostID) {
        return view('pdf', [
            'pdfFile' => $pdfFile,
            'titleOfDocu' => $title,
            'docuPostID' => $docuPostID,
        ]);
    })->name('view-pdf')->where('pdfFile', '.*');

    Route::get('/notification', function () {
        return view('pages.user.notification');
    })->name('user-notification');

    Route::get('/messages', function () {
        return view('pages.user.messages');
    })->name('user-messages');


    Route::get('/bookmark', Boomarks::class)->name('user-bookmark');
    Route::get('/temporary-view-pdf/{title?}/{pdfFile?}', function ($title = null, $pdfFile = null) {
        // dd('title:' . $title, 'pdffile:' . $pdfFile);
        return view('user_pages.user-temp-pdf-viewer', [
            'pdfFile' => $pdfFile,
            'titleOfDocu' => $title,
        ]);
    })->name('view-pdf-user-temp')->where('pdfFile', '.*');

});

// Route::get('/view-pdf{?pdfFile}', function () {
//     return view('pdf');
// })->name('view-pdf');






//no account needed
// Route::middleware(['user', 'guest'])->group( function (){
Route::get('/home', [HomeController::class, 'show'])->name('home');
Route::get('/home-component', Home::class)->name('home-component');
Route::get('/document/{reference?}', ViewDocuPost::class)->name('view-document');


Route::get('/search', function () {
    return view('pages.user.search');
})->name('user-search');

Route::get('/advanced-search', function () {
    return view('pages.user.advanced-search-page');
})->name('user-advanced-search');


Route::get('/search/Nsearch', [SearchController::class, 'basicSearch'])->name('basic-search');

Route::get('/search/result/', DocuSearchResult::class)->name('search-result-page');
Route::get('/advanced-search/result/', AdvancedSearchResults::class)->name('advanced-search-result-page');


Route::get('/catalogue', [CatalogueController::class, 'mainView'])->name('user-catalogue');
// });


//admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Route::get('/component', AddNewUser::class);
    // Route::post('list', AddNewUser::class)->name('admin.users');

    //list of users and adding process
    Route::get('/list', [UserController::class, 'studentList'])->name('admin.users');
    // Route::post('/list/add-user', [UserController::class, 'addNewUser'])->name('addNewUser');

    //designing
    Route::get('/home', Dashboard::class)->name('admin-home');
    Route::get('/view-document/{reference?}', ViewDocuPost::class)->name('admin-view-document');

    // Route::get('/users-panel', AdminUsersPanel::class)->name('admin-users-panel');
    Route::get('/users-panel', function () {
        return view('pages.admin.users-panel');
    })->name('admin-users-panel');

    Route::get('/documents-lists-panel', function () {
        return view('pages.admin.docu-panel-page');
    })->name('admin-docu-post-panel');
    // Route::get('/documents-lists-panel', DocuPostPanel::class)->name('admin-docu-post-panel');

    Route::get('/analytics', function () {
        return view('pages.admin.analytics');
    })->name('admin-analytics');

    Route::get('/reported-comments', function () {
        return view('pages.admin.reported_comments');
    })->name('admin-reported-comments');

    Route::prefix('setting')->group(function () {
        Route::get('/{activeTab?}', function () {
            return view('pages.admin.systemSetting');
        })->name('admin-system-setting');
    });

    Route::get('/users', function () {
        return view('pages.admin.users');
    })->name('admin-users');

    Route::get('/upload-document', UploadDocument::class)->name('upload-document-form');
    Route::get('/view-pdf/{title?}/{pdfFile?}', function ($title = null, $pdfFile = null) {
        // dd('title:' . $title, 'pdffile:' . $pdfFile);
        return view('pages.admin.admin_pdf_viewer', [
            'pdfFile' => $pdfFile,
            'titleOfDocu' => $title,
        ]);
    })->name('view-pdf-admin')->where('pdfFile', '.*');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::get('/signup', [UserController::class, 'register'])->name('register');
    Route::get('/forgot-password', function () {
        return view('forgot-password-page');
    })->name('forgot-pass');
});

Route::get('/sample', function () {
    return view('tester');
});

// Route::get('test', CreateUsers::class);


Route::get('/otp', [OtpAuthController::class, 'showForm']);
Route::post('/sentOtp', [OtpAuthController::class, 'sendOtp'])->name('sendOtp');
Route::post('/verifyOTP', function () {
    return view('verifyOtp');
})->name('verifyOTP');
Route::post('verifying', [OtpAuthController::class, 'checkOTP'])->name('verifyInputOTP');