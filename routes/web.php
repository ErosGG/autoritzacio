<?php

use App\Http\Controllers\DashboardController;
use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Facades\Route;

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

Route::get("/", function () {
    return view('welcome');
});

Route::get("/dashboard", [DashboardController::class, "index"])
    ->middleware(["auth"])
    ->name("links.index");

Route::post("/dashboard", [DashboardController::class, "create"])
    ->middleware(["auth"])
    ->name("link.create");

Route::delete("/links/{link}/", [DashboardController::class, "delete"])
    ->middleware(["auth"])
    ->name("link.delete");

Route::get("/links/{link}/", [DashboardController::class, "details"])
    ->middleware(["auth"])
    ->name("link.details");

Route::get("/links/{link}/edit/", [DashboardController::class, "edit"])
    ->middleware(["auth"])
    ->name("link.edit");

Route::put("/links/{link}/edit/", [DashboardController::class, "update"])
    ->middleware(["auth"])
    ->name("link.update");

Route::get("/count/{link}", function (Link $link) {
    $link->views++;
    $link->save();
    return redirect($link->url);
})->name("link.count");

require __DIR__."/auth.php";

Route::get("/{nick}", function ($nick) {
    $user = User::firstWhere("nick", $nick);
    if ($user) {
        $links = User::firstWhere("nick", $nick)->links()->get();
    } else {
        abort(404);
    }
    return view("show-links")->with("links", $links);
})->name("links.show");

