<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Models
use App\Models\certifications;
use App\Models\Employee;
use App\Models\Education;
use App\Models\Employment;
use App\Models\Licenses;
use App\Models\Loads;
use App\Models\orgs;
use App\Models\semconfig;
use App\Models\Trainings;
use App\Models\HiringHistory;
use App\Models\tags;

// Controllers
use App\Http\Controllers\PortalController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\EducationalBGController;
use App\Http\Controllers\EmploymentController;
use App\Http\Controllers\FilingController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\OrgController;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\ResPub;
use App\Http\Controllers\TrainingsController;
use App\Http\Controllers\certificationController;
use App\Http\Controllers\UpdatesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharepointController;

//
// All routes are protected by the "auth" middleware.
//
Route::middleware('auth','revalidate')->group(function () {

    /*------------------------------------------------------------------
    | 1. DASHBOARD
    |------------------------------------------------------------------*/
    Route::get('/hau_ep/dashboard', function () {
        $userInfo = Employee::where('emp_id', Auth::user()->id)->first();
        return view('portal.dashboard')->with(['userInfo' => $userInfo]);
    })->name('portal.dashboard');

    /*------------------------------------------------------------------
    | 2. PROFILE
    |------------------------------------------------------------------*/
    Route::get('/hau_ep/profile', function () {
        $userId = Auth::user()->id;
        $userInfo = Employee::where('emp_id', $userId)->first();
        return view('portal.profile', ['data' => $userInfo]);
    })->name('portal.profile');

    /*------------------------------------------------------------------
    | 3. TEACHING LOADS
    |------------------------------------------------------------------*/
    Route::get('hau_ep/teaching-loads', function () {
        // Get current semester configuration
        $reg_cs = semconfig::where('id', 1)->first();
        $tri_cs = semconfig::where('id', 2)->first();

        $loads = Loads::where('emp_id', Auth::user()->id)->get();
        $regular_loads = Loads::where('emp_id', Auth::user()->id)
            ->where('sy', $reg_cs->current_sy)
            ->where('semester', $reg_cs->current_sem)
            ->get();
        $trisem_loads = Loads::where('emp_id', Auth::user()->id)
            ->where('sy', $tri_cs->current_sy)
            ->where('semester', $tri_cs->current_sem)
            ->get();

        // Calculate units for regular and trisemester loads
        $sem_units = 0;
        foreach ($regular_loads as $i) {
            $sem_units += $i->subject->units;
        }
        $tri_units = 0;
        foreach ($trisem_loads as $i) {
            $tri_units += $i->subject->units;
        }

        $user = Employee::where('emp_id', Auth::user()->id)->first();
        return view('portal.loads')->with([
            'loads'         => $loads,
            'user'          => $user,
            'regular_loads' => $regular_loads,
            'trisem_loads'  => $trisem_loads,
            's_units'       => $sem_units,
            't_units'       => $tri_units,
            'sy'            => $reg_cs,
            't_sy'          => $tri_cs,
        ]);
    })->name('portal.loads');

    /*------------------------------------------------------------------
    | 4. HIRING HISTORY
    |------------------------------------------------------------------*/
    Route::get('hau_ep/hiring-history', function () {
        return view('portal.pages.hiring.main')->with([
            'hirings' => HiringHistory::where('emp_id', Auth::user()->id)
                ->orderBy('created_at', 'desc')->get(),
            'user'    => Employee::where('emp_id', Auth::user()->id)->first()
        ]);
    })->name('portal.hiring');

    /*------------------------------------------------------------------
    | 5. ORGANIZATIONS
    |------------------------------------------------------------------*/
    Route::get('hau_ep/organization-memberships', [OrgController::class, 'create'])
        ->name('portal.org');
    Route::get('hau_ep/organization-memberships/add', [OrgController::class, 'create_add'])
        ->name('portal.org.add');
    Route::get('hau_ep/organization-memberships/edit/{user}/{id}', [OrgController::class, 'create_edit'])
        ->where('id', '.*')
        ->name('portal.org.edit');
    Route::put('hau_ep/organization-memberships/update/{id}', [OrgController::class, 'update'])
        ->where('id', '.*')
        ->name('portal.org.update');
    Route::post('hau_ep/organization-memberships/store', [OrgController::class, 'store'])
        ->name('portal.org.store');
    Route::delete('hau_ep/organization-memberships/delete', [OrgController::class, 'destroy'])
        ->name('portal.org.delete');
    Route::delete('hau_ep/organization-memberships/clear', function () {
        orgs::where('emp_id', Auth::user()->id)->delete();
        return redirect()->route('portal.org')->with(['msg' => "All records deleted."]);
    })->name('portal.org.clear');

    /*------------------------------------------------------------------
    | 6. RESEARCH & PUBLICATION
    |------------------------------------------------------------------*/
    // Main Page & Submission Types
    Route::get('hau_ep/research-and-publications', [ResPub::class, 'create'])
        ->name('portal.respub');
    Route::get('hau_ep/research-and-publications/add-new', [ResPub::class, 'createType'])
        ->name('portal.respub.type');
    Route::get('hau_ep/research-and-publications/add-new/research', [ResPub::class, 'loadResearch'])
        ->name('portal.respub.add.research');
    Route::get('hau_ep/research-and-publications/add-new/publication', [ResPub::class, 'loadPublication'])
        ->name('portal.respub.add.publication');

    // Success and Edit pages
    Route::get('hau_ep/research-and-publications/add-new/success', function () {
        return view('portal.pages.respub.success');
    })->name('portal.respub.success');
    Route::get('hau_ep/research-and-publications/edit/success', function () {
        return view('portal.pages.respub.edit_success');
    })->name('portal.respub.edit_success');

    // View, Edit, and Update
    Route::get('hau_ep/research-and-publications/view/{id}', [ResPub::class, 'viewItem'])
        ->name('portal.respub.view');
    Route::get('hau_ep/research-and-publications/edit/{id}', [ResPub::class, 'createEdit'])
        ->name('portal.respub.edit');
    Route::put('hau_ep/research-and-publications/edit/{id}', [ResPub::class, 'editItem'])
        ->name('portal.respub.update');

    // Store new Research & Publication entries
    Route::post('hau_ep/research-and-publications/store-research', [ResPub::class, 'storeResearch'])
        ->name('portal.respub.store.research');
    Route::post('hau_ep/research-and-publications/store-publication', [ResPub::class, 'storePublication'])
        ->name('portal.respub.store.publication');

    // Delete route
    Route::delete('hau_ep/research-and-publications/delete/{id}', [ResPub::class, 'destroy'])
        ->name('portal.respub.delete');


    /*------------------------------------------------------------------
    | 7. CERTIFICATIONS
    |------------------------------------------------------------------*/
    Route::get('hau_ep/certifications', [certificationController::class, 'create'])
        ->name('portal.certifications');
    Route::get('hau_ep/certifications/approved', [certificationController::class, 'createApproved'])
        ->name('portal.certifications.approved');
    Route::get('hau_ep/certifications/pending', [certificationController::class, 'createPending'])
        ->name('portal.certifications.pending');
    Route::get('hau_ep/certifications/to-review', [certificationController::class, 'createToreview'])
        ->name('portal.certifications.toreview');
    Route::get('hau_ep/certifications/{id}', [certificationController::class, 'viewItem'])
        ->name('portal.certifications.view');
    Route::get('hau_ep/certifications/edit/{id}', [certificationController::class, 'edit'])
        ->name('portal.certifications.edit');
    Route::put('hau_ep/certifications/update/{id}', [certificationController::class, 'update'])
        ->name('portal.certifications.update');
    Route::delete('hau_ep/certifications/del/{id}', [certificationController::class, 'destroy'])
        ->name('portal.certifications.delete');

    /*------------------------------------------------------------------
    | 8. TRAININGS
    |------------------------------------------------------------------*/
    Route::get('hau_ep/trainings/', function () {
        $approvedTrainings = Trainings::where('emp_id', Auth::user()->id)
            ->where('status', 'Approved')
            ->orderBy('created_at', 'asc')->get();
        $approvedHAUCerts = certifications::where('emp_id', Auth::user()->id)
            ->where('status', 'Approved')
            ->whereNotNull('hau_cert')
            ->orderBy('created_at', 'asc')->get();
        $approved = $approvedTrainings->merge($approvedHAUCerts);
        $pendingTrainings = Trainings::where('emp_id', Auth::user()->id)
            ->where('status', 'Pending')
            ->orderBy('created_at', 'asc')->get();
        $pendingHAUCerts = certifications::where('emp_id', Auth::user()->id)
            ->where('status', 'Pending')
            ->whereNotNull('hau_cert')
            ->orderBy('created_at', 'asc')->get();
        $pending = $pendingTrainings->merge($pendingHAUCerts);
        $toreviewTrainings = Trainings::where('emp_id', Auth::user()->id)
            ->where('status', 'To-review')
            ->orderBy('created_at', 'asc')->get();
        $toreviewHAUCerts = certifications::where('emp_id', Auth::user()->id)
            ->where('status', 'To-review')
            ->whereNotNull('hau_cert')
            ->orderBy('created_at', 'asc')->get();
        $toreview = $toreviewTrainings->merge($toreviewHAUCerts);
        $trainings = Trainings::where('emp_id', Auth::user()->id)->get();
        $hauCerts = certifications::where('emp_id', Auth::user()->id)
                    ->whereNotNull('hau_cert')->get();
        $items = $trainings -> merge($hauCerts);

        return view('portal.pages.trainings.main')->with([
            'user'     => Employee::where('emp_id', Auth::user()->id)->first(),
            'items'    => $items,
            'approved' => $approved,
            'pending'  => $pending,
            'toreview' => $toreview,
        ]);
    })->name('portal.training');

    Route::get('hau_ep/trainings/{id}', function ($id) {
        return view('portal.pages.trainings.view')->with([
            'data' => Trainings::where('id', $id)->first()
        ]);
    })->name('portal.training.view');

    Route::get('hau_ep/trainings/edit/{id}', function ($id) {
        return view('portal.pages.trainings.edit')->with([
            'training_types' => tags::where('category', 'training_type')->get(),
            'data'         => Trainings::where('id', $id)->first()
        ]);
    })->name('portal.training.edit');

    Route::put('hau_ep/trainings/update/{id}', [TrainingsController::class, 'update'])
        ->name('portal.training.update');
    Route::delete('hau_ep/trainings/del/{id}', [TrainingsController::class, 'destroy'])
        ->name('portal.training.delete');
    Route::patch('hau_ep/trainings/resubmit/{id}', [TrainingsController::class, 'resubmit'])
        ->name('portal.training.resubmit');

    /*------------------------------------------------------------------
    | 9. LICENSES
    |------------------------------------------------------------------*/
    Route::get('hau_ep/licenses', function () {
        return view('portal.pages.license.main')->with([
            "approved" => Licenses::where('emp_id', Auth::user()->id)
                ->where('status', 'Approved')
                ->orderBy('date_obtained', 'desc')->get(),
            "pending"  => Licenses::where('status', 'Pending')
                ->orderBy('date_obtained', 'desc')->get(),
            "toreview" => Licenses::where('status', 'To-review')
                ->orderBy('date_obtained', 'desc')->get()
        ]);
    })->name('portal.license');

    Route::get('hau_ep/licenses/{id}', function ($id) {
        return view('portal.pages.license.view')->with([
            'data' => Licenses::where('id', $id)->first()
        ]);
    })->name('portal.license.view');

    Route::get('hau_ep/license/edit/{id}', function ($id) {
        return view('portal.pages.license.edit')->with([
            'data'          => Licenses::where('id', $id)->first(),
            'license_types' => tags::where('category', 'license_type')->get()
        ]);
    })->name('portal.license.edit');

    Route::put('hau_ep/license/update/{id}', [LicenseController::class, 'update'])
        ->name('portal.license.update');
    Route::delete('hau_ep/licenses/delete/{id}', [LicenseController::class, 'destroy'])
        ->name('portal.license.delete');

    /*------------------------------------------------------------------
    | 10. EDUCATIONAL BACKGROUND
    |------------------------------------------------------------------*/
    Route::get('hau_ep/education', function () {
        $approved = Education::where([
            'emp_id' => Auth::user()->id,
            'status' => 'Approved'
        ])->get();
        $pending = Education::where([
            'emp_id' => Auth::user()->id,
            'status' => 'Pending'
        ])->get();
        $toreview = Education::where([
            'emp_id' => Auth::user()->id,
            'status' => 'To-review'
        ])->get();

        return view('portal.pages.edu-bg.main')->with([
            'approved' => $approved,
            'pending'  => $pending,
            'toreview' => $toreview,
            'user'     => Employee::where('emp_id', Auth::user()->id)->first(),
            'msg'      => Str::length(session('msg')) > 0 ? session('msg') : null,
        ]);
    })->name('portal.edu-bg');

    Route::get('hau_ep/education/edit/{id}', function ($id) {
        return view('portal.pages.edu-bg.edit')->with([
            'data' => Education::where('id', $id)->first()
        ]);
    })->name('portal.edu-bg.edit');

    Route::put('hau_ep/education/update/{id}', [EducationalBGController::class, 'update'])
        ->name('portal.edu-bg.update');

    Route::get('hau_ep/education/{id}', function ($id) {
        $data = Education::where('id', $id)->first();
        return view('portal.pages.edu-bg.view')->with(['data' => $data]);
    })->name('portal.edu-bg.view');

    Route::delete('hau_ep/education/delete/{id}', [EducationalBGController::class, 'destroy'])
        ->name('portal.edu-bg.delete');

    /*------------------------------------------------------------------
    | 11. EMPLOYMENT
    |------------------------------------------------------------------*/
    Route::get('hau_ep/employment-history', function () {
        $approved = Employment::where([
            'emp_id' => Auth::user()->id,
            'status' => 'Approved'
        ])->get();
        $pending = Employment::where([
            'emp_id' => Auth::user()->id,
            'status' => 'Pending'
        ])->get();
        $toreview = Employment::where([
            'emp_id' => Auth::user()->id,
            'status' => 'To-review'
        ])->get();

        return view('portal.pages.employments.main')->with([
            'approved' => $approved,
            'pending'  => $pending,
            'toreview' => $toreview,
            'user'     => Employee::where('emp_id', Auth::user()->id)->first(),
            'msg'      => Str::length(session('msg')) > 0 ? session('msg') : null,
        ]);
    })->name('portal.employment');

    Route::get('hau_ep/employment-history/edit/{id}', function ($id) {
        return view('portal.pages.employments.edit')->with([
            'data' => Employment::where('id', $id)->first()
        ]);
    })->name('portal.employment.edit');

    Route::put('hau_ep/employment-history/update/{id}', [EmploymentController::class, 'update'])
        ->name('portal.employment.update');

    Route::get('hau_ep/employment-history/{id}', function ($id) {
        $data = Employment::where('id', $id)->first();
        return view('portal.pages.employments.view')->with(['data' => $data]);
    })->name('portal.employment.view');

    Route::delete('hau_ep/employment-history/delete/{id}', [EmploymentController::class, 'destroy'])
        ->name('portal.employment.delete');

    /*------------------------------------------------------------------
    | 12. DEPENDENTS
    |------------------------------------------------------------------*/
    Route::get('/hau_ep/dependents', [DependencyController::class, 'loadDependencies'])
        ->name('portal.dependencies');
    Route::get('/hau_ep/dependent/s', [DependencyController::class, 'searchDependency'])
        ->name('portal.dependencies.search');
    Route::get('/hau_ep/dependents/add', [DependencyController::class, 'loadAdd'])
        ->name('portal.dependencies.add');
    Route::get('hau_ep/dependents/edit/{id}', [DependencyController::class, 'loadEdit'])
        ->name('portal.dependencies.edit');
    Route::get('hau_ep/dependents/view/{id}', [DependencyController::class, 'viewItem'])
        ->name('portal.dependencies.view');
    Route::post('/hau_ep/dependents/add', [DependencyController::class, 'addDependent'])
        ->name('portal.dependencies.addnew');
    Route::delete('/hau_ep/dependents/del/{dep_id}', [DependencyController::class, 'destroy'])
        ->name('portal.dependencies.delete');
    Route::delete('/hau_ep/dependents/del', [DependencyController::class, 'clearAll'])
        ->name('portal.dependencies.clear');
    Route::put('hau_ep/dependents/update/{id}', [DependencyController::class, 'updateDependent'])
        ->name('portal.dependencies.update');

    /*------------------------------------------------------------------
    | 13. PENDING REQUESTS
    |------------------------------------------------------------------*/
    Route::get('hau_ep/pending-requests', [PendingController::class, 'create'])
        ->name('portal.pending');
    Route::get('hau_ep/pending-requests/{id}', [PendingController::class, 'viewInfo'])
        ->name('portal.pending.view');
    Route::delete('hau_ep/pending_requests/del/{id}', [PendingController::class, 'destroyRequest'])
        ->name('portal.pending.delete');

    /*------------------------------------------------------------------
    | 14. FILING APPLICATION
    |------------------------------------------------------------------*/
    Route::get('hau_ep/file-application/type', [FilingController::class, 'loadTypes'])
        ->name('portal.filing.type');
    Route::get('hau_ep/file-application/certification', [FilingController::class, 'loadCertification'])
        ->name('portal.filing.certification');
    Route::get('hau_ep/file-applicaton/education', function () {
        return view('portal.pages.filing.edu-bg');
    })->name('portal.filing.edu-bg');
    Route::get('hau_ep/file-applicaton/employment', function () {
        return view('portal.pages.filing.employment');
    })->name('portal.filing.employment');
    Route::get('hau_ep/file-application/license', function () {
        return view('portal.pages.filing.license')->with([
            'license_types' => tags::where('category', 'license_type')->get()
        ]);
    })->name('portal.filing.license');
    Route::get('hau_ep/file-application/training', function () {
        return view('portal.pages.filing.training')->with([
            'training_types' => tags::where('category', 'training_type')->get()
        ]);
    })->name('portal.filing.training');
    Route::get('hau_ep/file-application/success', [FilingController::class, 'loadSuccess'])
        ->name('portal.filing.success');

    // Filing - POST methods for adding new entries
    Route::post('hau_ep/file-application/certification/add', [certificationController::class, 'store'])
        ->name('portal.filing.certification.add');
    Route::post('hau_ep/file-application/education/add', [EducationalBGController::class, 'store'])
        ->name('portal.filing.edu-bg.add');
    Route::post('hau_ep/file-application/employment/add', [EmploymentController::class, 'store'])
        ->name('portal.filing.employment.add');
    Route::post('hau_ep/file-application/license/add', [LicenseController::class, 'store'])
        ->name('portal.filing.license.add');
    Route::post('hau_ep/file-application/training/add', [TrainingsController::class, 'store'])
        ->name('portal.filing.training.add');

    /*------------------------------------------------------------------
    | 15. ADDITIONAL PROFILE UPDATE ROUTES
    | (These routes update various parts of the user profile)
    |------------------------------------------------------------------*/
    Route::put('/hau_ep/profile/update/personal-data/{id}', [PortalController::class, 'updatePersonal'])
        ->name('portal.personal-data');
    Route::put('/hau_ep/profile/update/contact-information/{id}', [PortalController::class, 'updateContact'])
        ->name('portal.contact-information');
    Route::put('/hau_ep/profile/update/provincial-contact/{id}', [PortalController::class, 'updateProvincial'])
        ->name('portal.provincial-contact');
    Route::put('/hau_ep/profile/update/accounting-details/{id}', [PortalController::class, 'updateAccounting'])
        ->name('portal.accounting-details');
    Route::put('/hau_ep/profile/update/emergency/{id}', [PortalController::class, 'updateEmergency'])
        ->name('portal.emergency');

    // Profile Picture Update (action route)
    Route::put('/profile/update/change-picture/{id}', [UpdatesController::class, 'updatePic'])
        ->name('update-pic');

    /*------------------------------------------------------------------
    | 16. PROFILE EDIT VIEWS
    | (These routes display the edit forms for profile sections)
    |------------------------------------------------------------------*/
    // Edit Personal Data - with gender options
    Route::get('/hau_ep/profile/edit/personal-data', function () {
        $gender_tags = tags::where('category', 'gender')->get();
        $civil_status = tags::where('category', 'civil_status')->get();
        return view('portal.profile-edit.edit')->with([
            'gender_tags' => $gender_tags,
            'civil_status' => $civil_status,
            'data'        => Employee::where('emp_id', Auth::user()->id)->first()
        ]);
    })->name('portal.profile-edit-pd');

    // Edit Contact Information
    Route::get('/hau_ep/profile/edit/contact-information', function () {
        $data = Employee::where('emp_id', Auth::user()->id)->first();
        return view('portal.profile-edit.edit')->with(['data' => $data]);
    })->name('portal.profile-edit-ci');

    // Edit Provincial Contact
    Route::get('/hau_ep/profile/edit/provincial-contact', function () {
        $data = Employee::where('emp_id', Auth::user()->id)->first();
        return view('portal.profile-edit.edit')->with(['data' => $data]);
    })->name('portal.profile-edit-pc');

    // Edit Emergency Information
    Route::get('/hau_ep/profile/edit/emergency', function () {
        return view('portal.profile-edit.edit')->with([
            'data' => Employee::where('emp_id', Auth::user()->id)->first()
        ]);
    })->name('portal.profile-edit-ei');

    // Edit Accounting Details
    Route::get('/hau_ep/profile/edit/accounting-details', function () {
        $data = Employee::where('emp_id', Auth::user()->id)->first();
        return view('portal.profile-edit.edit')->with(['data' => $data]);
    })->name('portal.profile-edit-ad');

    // Change Profile Picture View (different from update action)
    Route::get('profile/change-profile-picture', [ProfileController::class, 'changepic'])
        ->name('profile.changepic');
    
    /*------------------------------------------------------------------
    | 17. SharePoint Sites
    |------------------------------------------------------------------*/
    Route::get('sharepoint-sites/dashboard', [SharepointController::class, 'index'])->name('sharepoint-sites.dashboard');
});
