<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

/*
| Public site.
*/
Route::get('/', [PageController::class, 'home'])->name('home');

Route::controller(PageController::class)->group(function () {
    Route::get('/services', 'services')->name('services');
    Route::get('/services/{service}', 'service')->whereIn('service', array_keys(config('site.services')))->name('service');
    Route::get('/plan-your-event', 'plan')->name('plan');
    Route::get('/about', 'about')->name('about');
    Route::get('/careers', 'careers')->name('careers');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/search', 'search')->name('search');
});

Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/portfolio/{project}', [PortfolioController::class, 'show'])->name('portfolio.show');

Route::post('/enquiries/{type}', [EnquiryController::class, 'store'])
    ->middleware('throttle:12,1')
    ->name('enquiries.store');

/*
| Staff admin.
*/
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AdminController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');

Route::middleware('auth')->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/enquiries', 'enquiries')->name('enquiries');
    Route::get('/enquiries/{enquiry}', 'enquiry')->name('enquiry');
    Route::patch('/enquiries/{enquiry}', 'updateEnquiry')->name('enquiry.update');
    Route::delete('/enquiries/{enquiry}', 'destroyEnquiry')->name('enquiry.destroy');
    Route::get('/enquiries/{enquiry}/attachment', 'attachment')->name('enquiry.attachment');
    Route::get('/portfolio', 'projects')->name('projects');
    Route::get('/portfolio/new', 'createProject')->name('projects.create');
    Route::post('/portfolio', 'storeProject')->name('projects.store');
    Route::get('/portfolio/{project}', 'editProject')->name('projects.edit');
    Route::put('/portfolio/{project}', 'updateProject')->name('projects.update');
    Route::delete('/portfolio/{project}', 'destroyProject')->name('projects.destroy');
    Route::get('/calendar', 'dates')->name('dates');
    Route::post('/calendar', 'storeDate')->name('dates.store');
    Route::delete('/calendar/{date}', 'destroyDate')->name('dates.destroy');
    Route::get('/careers', 'vacancies')->name('vacancies');
    Route::post('/careers', 'storeVacancy')->name('vacancies.store');
    Route::patch('/careers/{vacancy}', 'toggleVacancy')->name('vacancies.toggle');
    Route::delete('/careers/{vacancy}', 'destroyVacancy')->name('vacancies.destroy');
    Route::get('/announcements', 'announcements')->name('announcements');
    Route::post('/announcements', 'storeAnnouncement')->name('announcements.store');
    Route::patch('/announcements/{announcement}', 'toggleAnnouncement')->name('announcements.toggle');
    Route::delete('/announcements/{announcement}', 'destroyAnnouncement')->name('announcements.destroy');
});
