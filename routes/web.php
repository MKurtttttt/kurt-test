<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UpdatesController;
use App\Http\Controllers\SharepointController;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

// Debug: Remove this
Route::get('/test-email', function(){
    try{
        Mail::raw("Test email from Laravel!", function($message){
            $message->to('mqmallari@student.hau.edu.ph')
                ->subject('Test email');
        });
        return 'Email sent successfully! Check your inbox';
    } catch (\Exception $e){
        return 'Error: '. $e->getMessage();
    }
});


Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
 
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
 
            $user->save();
 
            event(new PasswordReset($user));
        }
    );
 
    return $status === Password::PasswordReset
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');




Route::get('/', function () {
    return view('homepage');
}) -> name ('home');

Route::get('/sharepoint', [SharepointController::class, 'publicIndex'])->name('sharepoint.public');

Route::get('/org-chart', function () {
    return view('home.orgchart');
})->name('orgchart');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [PasswordController::class, 'sendResetLinkEmail'])->name('password.email');


Route::get('/forgot-password', [PasswordController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->name('password.update');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLinkEmail'])
     ->middleware('throttle:5,10'); // 5 requests per 10 minutes

});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php'; 
require __DIR__.'/main_nav.php'; 
require __DIR__.'/prc.php'; 
require __DIR__.'/manage_emps.php'; 
require __DIR__.'/emp_portal.php'; 

//temp
require __DIR__.'/temp.php'; 