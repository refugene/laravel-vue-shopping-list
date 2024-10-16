<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ShoppingListController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shopping List
    Route::get('/shopping-list', [ShoppingListController::class, 'index'])->name('shoppingList.index');
    Route::post('/shopping-list', [ShoppingListController::class, 'store'])->name('shoppingList.store');
    Route::patch('/shopping-list/{id}/toggle', [ShoppingListController::class, 'toggleBought'])->name('shoppingList.toggleBought');
    Route::patch('/shopping-list/update-sort-order', [ShoppingListController::class, 'updateSortOrder'])->name('shoppingList.updateSortOrder');
    Route::delete('/shopping-list/{id}', [ShoppingListController::class, 'destroy'])->name('shoppingList.destroy');

});

require __DIR__.'/auth.php';
