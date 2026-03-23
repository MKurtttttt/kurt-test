<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Departments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\models\Employee;
use App\Models\Employee_Login;
use App\Models\requests;
use Illuminate\Support\Facades\Hash;

// use App\models\provincial_contact; 

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
 
        return view('auth.login');
        
    }

    /**
     * Handle an incoming authentication request.
     */

  
    public function store(LoginRequest $request)
    {


        $request -> validate([ 
            'email'=> 'required|email' 
        ]);
        //check if the email exists in the database, return if it doesn't exist
        if(!(Employee_Login::where('email', $request-> email)->exists())) { 
            return view('auth.login')-> with(['msg'=> 'No matching user found. Please try again.']); 
        }  


        
        
        //if it exists, get the data of the user  
        $user =Employee_Login::where('email' ,$request-> email)-> first();
        
        //check if the user account status is terminated
        if($user->terminated == 1) { 
            return view('auth.login')->with(['term_msg'=> 'Account deactivated. Please contact support.']); 
        }


        //check if the password matches, return if not
        if(!(Hash::check($request-> password, $user->password))) { 
            return view('auth.login')-> with(['msg'=> 'Password doesn’t match. Please try again.']); 
        } 
        
        Auth::login($user);

        if($user->role !== 'SuperAdmin') {
            session(['show_privacy_modal' => true]);
        }

        $request->session()->regenerate();

        if(Departments::where('code', $user->user->emp_dept)->exists()){ 
            session(['dept'=>true]); 
        } else { 
            session(['dept'=>false]); 
        }

        if(Auth::user()->role == 'SuperAdmin' || Auth::user()->role == 'HR Admin' || Auth::user()->role == 'Dean') { 
            return redirect()->route ('dashboard'); 
        }   else { 

            return redirect()->route('portal.dashboard');
        }
        


    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
