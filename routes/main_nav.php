<?php
// THE MAIN NAVIGATION ROUTES ARE HERE
use App\Models\Employee;
use App\Http\Controllers\PrcController;
use App\Http\Controllers\SharepointController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::middleware(['admin','revalidate'])->group(function() {
    Route::get('/dashboard', function () {
        $userInfo = Employee::where('emp_id' ,Auth::user()->id)->first(); 
        return view('dashboard')-> with(['userInfo'=> $userInfo]);
    })->name('dashboard');

    Route::get('manage-emps/dashboard', function() {
        $fname = Employee::where('emp_id', Auth::user()->id)->value('emp_fname'); 
        return view('manage-emps.manage-emps-dashboard')->with(['fname'=> $fname]); 
    })->name('manage-emps.dashboard');

    Route::get('scholarship-grants/dashboard', function() {
        $fname = Employee::where('emp_id', Auth::user()->id)->value('emp_fname'); 
        return view('scholarships-grants.scholarships-grants-dashboard')->with(['fname'=> $fname]); 
    })->name('scholarship-grants.dashboard');
  
    Route::get('sharepoint-sites/dashboard', [SharepointController::class, 'index'])->name('sharepoint-sites.dashboard');
  
    Route::get('/under-construction', function () {
        return view('construction');
    })->name('construction');


})
?>