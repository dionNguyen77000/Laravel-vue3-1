<?php

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Reminder\PostController;
use App\Http\Controllers\DataTable\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::get('posts', [PostController::class,'index']);
Route::resource('posts', PostController::class);
// Route::post('/posts', [PostController::class, 'store']);

Route::resource('datatable/users', UserController::class);

Route::group(['prefix' => 'auth', 'namespace'=> 'Auth'], function () {
    Route::post('register', [RegisterController::class, 'action'])->name('register');
    Route::post('login', [LoginController::class, 'action'])->name('login');
    Route::post('logout', [LogoutController::class, 'action'])->name('logout');
    Route::get('me', [MeController::class, 'action'])->name('me');
});

// Route::group(['prefix' => 'auth'], function () {
//     Route::post('register', RegisterController::class)->name('login');
//     Route::post('login', LoginController::class);
//     Route::get('me', MeController::class);
// });


// Route::get('/admin/users', 'Admin\UserController@index');
// Route::get('/admin/plans', 'Admin\PlanController@index');


// Route::get('/test_user', function (\Illuminate\Http\Request $request) {
//     $user = $request->user();
//     $user->withdrawPermissionTo('delete posts', 'edit posts');
//     dump($user->can('delete posts'));
  
// });

// Route::get('/dashboard', [DashboardController::class, 'index'])
//     ->name('dashboard');

// Route::get('/users/{user:username}/posts', [UserPostController::class, 'index'])->name('users.posts');

// Route::get('/register', [RegisterController::class, 'index'])->name('register');
// Route::post('/register', [RegisterController::class, 'store']);

// Route::get('/login', [LoginController::class, 'index'])->name('login');
// Route::post('/login', [LoginController::class, 'store']);

// Route::post('/logout', [LogoutController::class, 'store'])->name('logout');

// Route::get('/posts', function () {
//     return view('posts.index');
// });

// Route::get('/posts', [PostController::class, 'index'])->name('posts');
// Route::get('/posts_vue', [PostController::class, 'index_vue'])->name('posts_vue');
// Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
// Route::post('/posts', [PostController::class, 'store']);
// Route::post('/posts_vue', [PostController::class, 'store_vue']);
// Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
// Route::delete('/posts_vue/{post}', [PostController::class, 'destroy_vue'])->name('posts_vue.destroy');


// Route::post('/posts/{id}/likes', [PostLikeController::class, 'store']) -> name('posts.likes') ;


// -------role and permission midlleware -----
// Route::post('/posts/{post}/likes', [PostLikeController::class, 'store'])->name('posts.likes');
// Route::delete('/posts/{post}/likes', [PostLikeController::class, 'destroy'])->name('posts.likes');



// Route::group(['middleware' => 'role:admin'], function () {
//     Route::group(['middleware' => 'role:admin,delete users'], function () {
//         Route::get('/admin/users', function () {
//             return 'Delete users in admin panel';
//         });
//     });

//     Route::get('/admin', function () {
//         return 'Admin panel';
//     });
// });

// data table