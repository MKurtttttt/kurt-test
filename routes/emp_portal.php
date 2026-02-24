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
use App\Http\Controllers\KpiController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\OrgController;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\ResPub;
use App\Http\Controllers\TrainingsController;
use App\Http\Controllers\certificationController;
use App\Http\Controllers\UpdatesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharepointController;
use App\Http\Controllers\KnowledgeHubController;
use App\Http\Controllers\IsoDocumentController;
use App\Http\Controllers\IsoManagementController;
use App\Http\Controllers\VisitorController;

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
    | 3. EMPLOYMENT AND ACADEMIC MODULE
    |------------------------------------------------------------------*/
    Route::get('/emp-acad-module', function () {
        $userInfo = Employee::where('emp_id', Auth::user()->id)->first();
        return view('portal.emp-acad-module')->with(['userInfo' => $userInfo]);
    })->name('portal.emp-acad-module');

    /*------------------------------------------------------------------
    | 4. TEACHING LOADS
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/teaching-loads', function () {
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
    | 5. HIRING HISTORY
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/hiring-history', function () {
        return view('portal.pages.hiring.main')->with([
            'hirings' => HiringHistory::where('emp_id', Auth::user()->id)
                ->orderBy('created_at', 'desc')->get(),
            'user'    => Employee::where('emp_id', Auth::user()->id)->first()
        ]);
    })->name('portal.hiring');

    /*------------------------------------------------------------------
    | 6. ORGANIZATIONS
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/organization-memberships', [OrgController::class, 'create'])
        ->name('portal.org');
    Route::get('emp-acad-module/organization-memberships/add', [OrgController::class, 'create_add'])
        ->name('portal.org.add');
    Route::get('emp-acad-module/organization-memberships/edit/{user}/{id}', [OrgController::class, 'create_edit'])
        ->where('id', '.*')
        ->name('portal.org.edit');
    Route::put('emp-acad-module/organization-memberships/update/{id}', [OrgController::class, 'update'])
        ->where('id', '.*')
        ->name('portal.org.update');
    Route::post('emp-acad-module/organization-memberships/store', [OrgController::class, 'store'])
        ->name('portal.org.store');
    Route::delete('emp-acad-module/organization-memberships/delete', [OrgController::class, 'destroy'])
        ->name('portal.org.delete');
    Route::delete('emp-acad-module/organization-memberships/clear', function () {
        orgs::where('emp_id', Auth::user()->id)->delete();
        return redirect()->route('portal.org')->with(['msg' => "All records deleted."]);
    })->name('portal.org.clear');

    /*------------------------------------------------------------------
    | 7. RESEARCH & PUBLICATION
    |------------------------------------------------------------------*/
    // Main Page & Submission Types
    Route::get('emp-acad-module/research-and-publications', [ResPub::class, 'create'])
        ->name('portal.respub');
    Route::get('emp-acad-module/research-and-publications/add-new', [ResPub::class, 'createType'])
        ->name('portal.respub.type');
    Route::get('emp-acad-module/research-and-publications/add-new/research', [ResPub::class, 'loadResearch'])
        ->name('portal.respub.add.research');
    Route::get('emp-acad-module/research-and-publications/add-new/publication', [ResPub::class, 'loadPublication'])
        ->name('portal.respub.add.publication');

    // Success and Edit pages
    Route::get('emp-acad-module/research-and-publications/add-new/success', function () {
        return view('portal.pages.respub.success');
    })->name('portal.respub.success');
    Route::get('emp-acad-module/research-and-publications/edit/success', function () {
        return view('portal.pages.respub.edit_success');
    })->name('portal.respub.edit_success');

    // View, Edit, and Update
    Route::get('emp-acad-module/research-and-publications/view/{id}', [ResPub::class, 'viewItem'])
        ->name('portal.respub.view');
    Route::get('emp-acad-module/research-and-publications/edit/{id}', [ResPub::class, 'createEdit'])
        ->name('portal.respub.edit');
    Route::put('emp-acad-module/research-and-publications/edit/{id}', [ResPub::class, 'editItem'])
        ->name('portal.respub.update');

    // Store new Research & Publication entries
    Route::post('emp-acad-module/research-and-publications/store-research', [ResPub::class, 'storeResearch'])
        ->name('portal.respub.store.research');
    Route::post('emp-acad-module/research-and-publications/store-publication', [ResPub::class, 'storePublication'])
        ->name('portal.respub.store.publication');

    // Delete route
    Route::delete('emp-acad-module/research-and-publications/delete/{id}', [ResPub::class, 'destroy'])
        ->name('portal.respub.delete');


    /*------------------------------------------------------------------
    | 8. CERTIFICATIONS
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/certifications', [certificationController::class, 'create'])
        ->name('portal.certifications');
    Route::get('emp-acad-module/certifications/approved', [certificationController::class, 'createApproved'])
        ->name('portal.certifications.approved');
    Route::get('emp-acad-module/certifications/pending', [certificationController::class, 'createPending'])
        ->name('portal.certifications.pending');
    Route::get('emp-acad-module/certifications/to-review', [certificationController::class, 'createToreview'])
        ->name('portal.certifications.toreview');
    Route::get('emp-acad-module/certifications/{id}', [certificationController::class, 'viewItem'])
        ->name('portal.certifications.view');
    Route::get('emp-acad-module/certifications/edit/{id}', [certificationController::class, 'edit'])
        ->name('portal.certifications.edit');
    Route::put('emp-acad-module/certifications/update/{id}', [certificationController::class, 'update'])
        ->name('portal.certifications.update');
    Route::delete('emp-acad-module/certifications/del/{id}', [certificationController::class, 'destroy'])
        ->name('portal.certifications.delete');

    /*------------------------------------------------------------------
    | 9. TRAININGS
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/trainings/', function () {
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

    Route::get('emp-acad-module/trainings/{id}', function ($id) {
        return view('portal.pages.trainings.view')->with([
            'data' => Trainings::where('id', $id)->first()
        ]);
    })->name('portal.training.view');

    Route::get('emp-acad-module/trainings/edit/{id}', function ($id) {
        return view('portal.pages.trainings.edit')->with([
            'training_types' => tags::where('category', 'training_type')->get(),
            'data'         => Trainings::where('id', $id)->first()
        ]);
    })->name('portal.training.edit');

    Route::put('emp-acad-module/trainings/update/{id}', [TrainingsController::class, 'update'])
        ->name('portal.training.update');
    Route::delete('emp-acad-module/trainings/del/{id}', [TrainingsController::class, 'destroy'])
        ->name('portal.training.delete');
    Route::patch('emp-acad-module/trainings/resubmit/{id}', [TrainingsController::class, 'resubmit'])
        ->name('portal.training.resubmit');

    /*------------------------------------------------------------------
    | 10. LICENSES
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/licenses', function () {
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

    Route::get('emp-acad-module/licenses/{id}', function ($id) {
        return view('portal.pages.license.view')->with([
            'data' => Licenses::where('id', $id)->first()
        ]);
    })->name('portal.license.view');

    Route::get('emp-acad-module/license/edit/{id}', function ($id) {
        return view('portal.pages.license.edit')->with([
            'data'          => Licenses::where('id', $id)->first(),
            'license_types' => tags::where('category', 'license_type')->get()
        ]);
    })->name('portal.license.edit');

    Route::put('emp-acad-module/license/update/{id}', [LicenseController::class, 'update'])
        ->name('portal.license.update');
    Route::delete('emp-acad-module/licenses/delete/{id}', [LicenseController::class, 'destroy'])
        ->name('portal.license.delete');

    /*------------------------------------------------------------------
    | 11. EDUCATIONAL BACKGROUND
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/education', function () {
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

    Route::get('emp-acad-module/education/edit/{id}', function ($id) {
        return view('portal.pages.edu-bg.edit')->with([
            'data' => Education::where('id', $id)->first()
        ]);
    })->name('portal.edu-bg.edit');

    Route::put('emp-acad-module/education/update/{id}', [EducationalBGController::class, 'update'])
        ->name('portal.edu-bg.update');

    Route::get('emp-acad-module/education/{id}', function ($id) {
        $data = Education::where('id', $id)->first();
        return view('portal.pages.edu-bg.view')->with(['data' => $data]);
    })->name('portal.edu-bg.view');

    Route::delete('emp-acad-module/education/delete/{id}', [EducationalBGController::class, 'destroy'])
        ->name('portal.edu-bg.delete');

    /*------------------------------------------------------------------
    | 12. EMPLOYMENT
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/employment-history', function () {
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

    Route::get('emp-acad-module/employment-history/edit/{id}', function ($id) {
        return view('portal.pages.employments.edit')->with([
            'data' => Employment::where('id', $id)->first()
        ]);
    })->name('portal.employment.edit');

    Route::put('emp-acad-module/employment-history/update/{id}', [EmploymentController::class, 'update'])
        ->name('portal.employment.update');

    Route::get('emp-acad-module/employment-history/{id}', function ($id) {
        $data = Employment::where('id', $id)->first();
        return view('portal.pages.employments.view')->with(['data' => $data]);
    })->name('portal.employment.view');

    Route::delete('emp-acad-module/employment-history/delete/{id}', [EmploymentController::class, 'destroy'])
        ->name('portal.employment.delete');

    /*------------------------------------------------------------------
    | 13. DEPENDENTS
    |------------------------------------------------------------------*/
    Route::get('/emp-acad-module/dependents', [DependencyController::class, 'loadDependencies'])
        ->name('portal.dependencies');
    Route::get('/emp-acad-module/dependent/s', [DependencyController::class, 'searchDependency'])
        ->name('portal.dependencies.search');
    Route::get('/emp-acad-module/dependents/add', [DependencyController::class, 'loadAdd'])
        ->name('portal.dependencies.add');
    Route::get('emp-acad-module/dependents/edit/{id}', [DependencyController::class, 'loadEdit'])
        ->name('portal.dependencies.edit');
    Route::get('emp-acad-module/dependents/view/{id}', [DependencyController::class, 'viewItem'])
        ->name('portal.dependencies.view');
    Route::post('/emp-acad-module/dependents/add', [DependencyController::class, 'addDependent'])
        ->name('portal.dependencies.addnew');
    Route::delete('/emp-acad-module/dependents/del/{dep_id}', [DependencyController::class, 'destroy'])
        ->name('portal.dependencies.delete');
    Route::delete('/emp-acad-module/dependents/del', [DependencyController::class, 'clearAll'])
        ->name('portal.dependencies.clear');
    Route::put('emp-acad-module/dependents/update/{id}', [DependencyController::class, 'updateDependent'])
        ->name('portal.dependencies.update');

    /*------------------------------------------------------------------
    | 14. PENDING REQUESTS
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/pending-requests', [PendingController::class, 'create'])
        ->name('portal.pending');
    Route::get('emp-acad-module/pending-requests/{id}', [PendingController::class, 'viewInfo'])
        ->name('portal.pending.view');
    Route::delete('emp-acad-module/pending_requests/del/{id}', [PendingController::class, 'destroyRequest'])
        ->name('portal.pending.delete');

    /*------------------------------------------------------------------
    | 15. FILING APPLICATION
    |------------------------------------------------------------------*/
    Route::get('emp-acad-module/file-application/type', [FilingController::class, 'loadTypes'])
        ->name('portal.filing.type');
    Route::get('emp-acad-module/file-application/certification', [FilingController::class, 'loadCertification'])
        ->name('portal.filing.certification');
    Route::get('emp-acad-module/file-applicaton/education', function () {
        return view('portal.pages.filing.edu-bg');
    })->name('portal.filing.edu-bg');
    Route::get('emp-acad-module/file-applicaton/employment', function () {
        return view('portal.pages.filing.employment');
    })->name('portal.filing.employment');
    Route::get('emp-acad-module/file-application/license', function () {
        return view('portal.pages.filing.license')->with([
            'license_types' => tags::where('category', 'license_type')->get()
        ]);
    })->name('portal.filing.license');
    Route::get('emp-acad-module/file-application/training', function () {
        return view('portal.pages.filing.training')->with([
            'training_types' => tags::where('category', 'training_type')->get()
        ]);
    })->name('portal.filing.training');
    Route::get('emp-acad-module/file-application/success', [FilingController::class, 'loadSuccess'])
        ->name('portal.filing.success');

    // Filing - POST methods for adding new entries
    Route::post('emp-acad-module/file-application/certification/add', [certificationController::class, 'store'])
        ->name('portal.filing.certification.add');
    Route::post('emp-acad-module/file-application/education/add', [EducationalBGController::class, 'store'])
        ->name('portal.filing.edu-bg.add');
    Route::post('emp-acad-module/file-application/employment/add', [EmploymentController::class, 'store'])
        ->name('portal.filing.employment.add');
    Route::post('emp-acad-module/file-application/license/add', [LicenseController::class, 'store'])
        ->name('portal.filing.license.add');
    Route::post('emp-acad-module/file-application/training/add', [TrainingsController::class, 'store'])
        ->name('portal.filing.training.add');

    /*------------------------------------------------------------------
    | 16. ADDITIONAL PROFILE UPDATE ROUTES
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
    | 17. PROFILE EDIT VIEWS
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
    | 18. SharePoint Sites
    |------------------------------------------------------------------*/
    Route::get('sharepoint-sites/dashboard', [SharepointController::class, 'index'])->name('sharepoint-sites.dashboard');

    /*------------------------------------------------------------------
    | 19. Knowledge Hub
    |------------------------------------------------------------------*/
    Route::get('knowledge-hub/dashboard', [KnowledgeHubController::class, 'index'])->name('knowledge-hub.dashboard');
    
    /*------------------------------------------------------------------
    | 20. KPI Library
    |------------------------------------------------------------------*/
    Route::get('/kpis/dashboard', [KpiController::class, 'dashboard'])->name('kpis.dashboard');
    
    // Export KPI List to Excel
    Route::get('/kpis/export', [KpiController::class, 'export'])->name('kpis.export-all');

    // Advanced search for KPI library
    Route::get('/kpis/advanced-search', [KpiController::class, 'advancedSearch'])->name('kpis.advanced-search');
    
    // View individual KPI details
    Route::get('/kpis/{kpi}', [KpiController::class, 'showKpiView'])->name('kpis.show');
    
    // Export Inidividual KPI to Excel
    Route::get('/kpis/{kpi}/export', [KpiController::class, 'export'])->name('kpis.export');

    /*------------------------------------------------------------------
    | 21. ISO Document Handling
    |------------------------------------------------------------------*/
    Route::get('/iso/document', [IsoDocumentController::class, 'loadDocument'])
        ->name('iso.document');
    Route::post('/iso/document/store', [IsoDocumentController::class,'storeDocument'])
        ->name('iso.document.store');

    // the {ticket} here means that Laravel automatically converts to a model instance.
    Route::get('/iso/document/{ticket}/edit', [IsoDocumentController::class,'editDocument'])
        ->name('iso.document.edit');
    Route::put('/iso/document/{ticket}', [IsoDocumentController::class,'updateDocument'])
        ->name('iso.document.update');
    // Route to get documents by office
    Route::get('/iso/documents/by-office', [IsoDocumentController::class, 'getDocumentsByOffice'])
        ->name('iso.documents.by_office');

    Route::delete('/iso/document/{ticket}', [IsoDocumentController::class,'destroyDocument'])
        ->name('iso.document.destroy');
    // PATCH route updates the ticket status (Meant only for partial updates on the ticket)
    Route::patch('/iso/document/{ticket}/submit', [IsoDocumentController::class,'submitToIDC'])
        ->name('iso.document.submit');

    /*------------------------------------------------------------------
    | 22. ISO IDC Document Handling
    |------------------------------------------------------------------*/
    // IDC Management Routes
    Route::get('/iso/idc/dashboard', [IsoDocumentController::class,'loadIdcDashboard'])
        ->name('iso.idc.dashboard');
    Route::patch('iso/idc/{ticket}/update-status', [IsoDocumentController::class,'updateTicketStatus'])
        ->name('iso.idc.-update-status');
    Route::patch('/iso/idc/{documentId}/status', [IsoDocumentController::class, 'updateDocumentStatus'])
        ->name('iso.idc.status.update');
    Route::delete('/iso/idc/reset-system', [IsoDocumentController::class, 'resetSystem'])
        ->name('iso.idc.reset.system');
    
    /*------------------------------------------------------------------
    | 23. ISO Document Management
    |------------------------------------------------------------------*/
    Route::patch('/iso/idc/{ticket}/register', [IsoDocumentController::class, 'registerTicket'])
        ->name('iso.idc.register.ticket');
    Route::get('/iso/management', [IsoManagementController::class, 'index'])
        ->name('iso.management.index');
    Route::get('/iso/management/documents', [IsoManagementController::class, 'getDocuments'])
        ->name('iso.management.documents');

    // Import/Export Routes
    Route::post('iso/management/import', [IsoManagementController::class, 'import'])
        ->name('iso.management.import');
    Route::get('iso/management/export', [IsoManagementController::class, 'export'])
        ->name('iso.management.export');
    Route::get('iso/management/template', [IsoManagementController::class, 'downloadTemplate'])
        ->name('iso.management.template');

    /*------------------------------------------------------------------
    | 24. Visit Counter
    |------------------------------------------------------------------*/
    Route::get('/visitor-count/visitors', [VisitorController::class, 'index'])
        ->name('visitor-count.dashboard');
    Route::post('/visitor-count/visitors/homepage-period', [VisitorController::class, 'setHomepagePeriod'])
        ->name('visitor-count.homepage-period');
    Route::delete('/visitor-count/clear', [VisitorController::class, 'clearAll'])
        ->name('visitor-count.clear');
});
