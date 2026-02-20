<?php

use App\Http\Controllers\AdminOrgController;
use App\Http\Controllers\DeptController;
use App\Http\Controllers\HiringHistoryController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\KpiSegmentationController;
use App\Http\Controllers\KpiDimensionController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SharepointController;
use App\Http\Controllers\InformationHubController;
use App\Http\Controllers\UserRecordsController;
use App\Http\Controllers\UserTerminationController;
use App\Models\Employee;
use App\Models\Departments;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

function updated_date($date) { 
    $initial_d = explode(' ', $date); //initial date

    switch(explode('-', $initial_d[0])[1]){
        case 1:
            $month = 'January';
            break;
        case 2:
            $month = 'February';
            break;
        case 3:
            $month = 'March';
            break;
        case 4:
            $month = 'April';
            break;
        case 5:
            $month = 'May';
            break;
        case 6:
            $month = 'June';
            break;
        case 7:
            $month = 'July';
            break;
        case 8:
            $month = 'August';
            break;
        case 9:
            $month = 'September';
            break;
        case 10:
            $month = 'October';
            break;
        case 11:
            $month = 'November';
            break;
        case 12:
            $month = 'December';
            break;
    }
    
    return $month . ' ' . explode('-', $initial_d[0])[2] . ', ' . explode('-', $initial_d[0])[0];
}

Route::middleware(['superOrHR','revalidate'])->group(function() { 
    Route::get('admin/dashboard', function() {
        $fname = Employee::where('emp_id', Auth::user()->id)->value('emp_fname'); 
        return view('admin.dashboard')->with(['fname'=> $fname]); 
    })->name('admin.dashboard');
    
    /*************** RECORDS ****************/
    Route::get('admin/records', function() { 
        return view('admin.records.dashboard'); 
    })->name('admin.records'); 

    /**************** RECORDS -- USER  *****/
    Route::get('admin/records/users', [UserRecordsController::class, 'index'])->name('admin.users');
    Route::get('admin/records/users/add/{origin?}', [UserRecordsController::class, 'add'])->name('admin.users.add');
    Route::get('admin/records/users/addMultiple/{origin?}', [UserRecordsController::class, 'addMultiple'])->name('admin.users.addMultiple');
    Route::post('admin/records/users/addMultiple', [UserRecordsController::class, 'load_multiple_users'])->name('admin.users.addMultiple.load');
    Route::post('admin/records/users/addMultiple/save', [UserRecordsController::class, 'save_multiple_users'])->name('admin.users.addMultiple.save');
    Route::post('admin/records/users', [UserRecordsController::class, 'store'])->name('admin.users.store');
    Route::get('admin/subjects/search/', [AdminOrgController::class, 'search'])->name('admin.orgs.search'); 

    Route::get('admin/users/search/', [UserRecordsController::class, 'search'])->name('admin.users.search'); 

    Route::delete('admin/records/organizations/destroy/{id}', [AdminOrgController::class, 'destroy'])->name('admin.orgs.destroy'); // Individual deleting

    Route::delete('admin/records/organizations/delete', [AdminOrgController::class, 'delete'])->name('admin.orgs.delete'); // Batch deleting
    
    Route::get('admin/records/users/{id}', [UserRecordsController::class, 'view_user'])->name('admin.users.view');
    Route::get('admin/records/users/item/{id}', [UserRecordsController::class, 'viewItem'])->name('admin.users.viewItem');
    Route::get('admin/records/users/{id}/edit/{section?}', 
        [UserRecordsController::class, 'edit_user']
    )->name('admin.users.edit');

    Route::put('admin/records/users/{id}/edit/{section?}', 
        [UserRecordsController::class, 'update_user']
    )->name('admin.users.update');
    Route::get('admin/records/user/filter/{type}', [UserRecordsController::class, 'filter'])->name('admin.users.filter');

    Route::post('admin/records/users/item/{id}/approve', [UserRecordsController::class, 'approveItem'])->name('admin.users.approve');
    Route::post('admin/records/users/item/{id}/toReview', [UserRecordsController::class, 'toReviewItem'])->name('admin.users.toreview');

    // Termination of users
    Route::put('admin/records/users/terminate/{id}', [UserTerminationController::class,'terminate'])->name('admin.users.terminate'); 
    Route::put('admin/records/users/activate/{id}',[UserTerminationController::class, 'activate'])->name('admin.users.activate'); 

    /**************** RECORDS -- USER - HIRING HISTORY  *****/
    Route::get('admin/records/users/{id}/hiring/edit', [HiringHistoryController::class, 'loadHiring'])->name('admin.hiring'); 

    Route::put('admin.records/users/{id}/hiring/update', [HiringHistoryController::class, 'updateHiring'])->name('admin.hiring.save'); 

   
    
Route::middleware(['superAdmin','revalidate'])->group(function() { 
    /******************* APP CONFIGS */
    Route::get('admin/registry', function() { 
        return view('admin.config.db'); 
    })->name('admin.registry'); 

    Route::get('admin/registry/departments/{id}', [DeptController::class, 'view_department'])->name('admin.dept.view'); 

    Route::get('admin/registry/departments', function(){
        $dept = Departments::orderBy('dept', 'asc')->paginate(10); 

        if(Departments::count() != 0) { 
            $updated = Departments::orderBy('updated_at', 'desc')->first()->value('updated_at');
        } else {
            $updated = now(); 
        }

        return view('admin.config.dept.departments')->with([
            'dept'=> $dept,
            'msg'=> session('msg'), 
            'u_at'=> updated_date($updated)
        ]);
    })->name('admin.registry.dept'); 

    Route::get('admin/registry/department/update/all', function() { 
        return view('admin.config.dept.update-all');
    })->name('admin.registry.dept.edit.all'); 

    Route::get('admin/registry/department/files',[DeptController::class, 'load_list'])->name("admin.registry.dept.files");

    Route::get('admin/registry/department/files/dl/{file}', [DeptController::class, 'download_sheet'])->name('registry.dept.files.download'); 

    Route::get('admin/registry/department/files/dl_temp/{file}', [DeptController::class, 'download_template'])->name('registry.dept.files.download_temp'); 

    Route::get('admin/registry/department/search',[DeptController::class, 'search'])->name('admin.dept.search');

    Route::post('admin/registry/department/update/all', [DeptController::class, 'load_all_file'])->name('admin.registry.dept.edit.all.load'); 

    Route::post('admin/registry/department/update/dept', [DeptController::class, 'load_dept_file'])->name('admin.registry.dept.edit.load');

    Route::post('admin/registry/department/update/all/save', [DeptController::class, 'update_all'])->name('admin.registry.dept.edit.all.save');

    // Individual editing of records
    Route::put('admin/registry/departments/{id}/edit', [DeptController::class, 'update_dept'])->name('admin.dept.update'); 

    Route::delete('admin/registry/departments/delete/{id}', [DeptController::class, 'destroy'])->name('admin.dept.delete'); 

    ///app config - semesters 
    Route::get('admin/registry/semester',[SemesterController::class, 'index'])->name('admin.sem');

    Route::put('admin/registry/semester/update', [SemesterController::class, 'updateRegular'])->name('admin.sem.updatereg'); 

    Route::put('admin/registry/semester/update/tri', [SemesterController::class, 'updateTrimester'])->name('admin.sem.updatetri');

    /************
     * USER ORGANIZATIONS 
     ************/
    Route::get('admin/records/organizations', [AdminOrgController::class, 'loadOrgs'])->name('admin.orgs'); 
    Route::get('admin/records/organizations/{id}', [AdminOrgController::class, 'loadOrganization'])->name('admin.orgs.view'); 


    /******************** SHAREPOINT LINKS ********************/
    // Add sharepoint link (form)
    Route::get('sharepoint-sites/add', [SharepointController::class, 'create'])->name('sharepoint-sites.add');

    // Store new sharepoint link
    Route::post('sharepoint-sites/store', [SharepointController::class, 'store'])->name('sharepoint-sites.store');

    // Edit sharepoint link (form)
    Route::get('sharepoint-sites/edit/{id}', [SharepointController::class, 'edit'])->name('sharepoint-sites.edit'); 
    Route::get('sharepoint-sites/edit', [SharepointController::class, 'editList'])->name('sharepoint-sites.edit-list'); 

    // Select link to edit (optional if used separately)
    Route::get('sharepoint-sites/select', [SharepointController::class, 'selectForm'])->name('sharepoint-sites.select-form');
    Route::post('sharepoint-sites/select', [SharepointController::class, 'select'])->name('sharepoint-sites.select');

    // Update sharepoint link
    Route::put('sharepoint-sites/update/{id}', [SharepointController::class, 'update'])->name('sharepoint-sites.update');

    // Delete sharepoint link
    Route::delete('sharepoint-sites/delete/{id}', [SharepointController::class, 'destroy'])->name('sharepoint-sites.delete');

    /******************** INFORMATION HUB LINKS ********************/
    // Add information hub link (form)
    Route::get('information-hub/add', [InformationHubController::class, 'create'])->name('information-hub.add');

    // Store new information hub link
    Route::post('information-hub/store', [InformationHubController::class, 'store'])->name('information-hub.store');

    // Edit information hub link (form)
    Route::get('information-hub/edit/{id}', [InformationHubController::class, 'edit'])->name('information-hub.edit'); 
    Route::get('information-hub/edit', [InformationHubController::class, 'editList'])->name('information-hub.edit-list'); 

    // Select link to edit (optional if used separately)
    Route::get('information-hub/select', [InformationHubController::class, 'selectForm'])->name('information-hub.select-form');
    Route::post('information-hub/select', [InformationHubController::class, 'select'])->name('information-hub.select');

    // Update information hub link
    Route::put('information-hub/update/{id}', [InformationHubController::class, 'update'])->name('information-hub.update');

    // Delete information hub link
    Route::delete('information-hub/delete/{id}', [InformationHubController::class, 'destroy'])->name('information-hub.delete');

    /******************** KPI Library ********************/
    Route::get('/kpis/add', [KpiController::class, 'create'])->name('kpis.add');
    Route::post('/kpis', [KpiController::class, 'store'])->name('kpis.store');
    Route::get('/kpis/{kpi}/edit', [KpiController::class, 'edit'])->name('kpis.edit');
    Route::put('/kpis/{kpi}', [KpiController::class, 'update'])->name('kpis.update');
    Route::delete('/kpis/{kpi}', [KpiController::class, 'destroy'])->name('kpis.destroy');


    // KPI Segmentation CRUD routes
    Route::get('/segmentations/create', [KpiSegmentationController::class, 'create'])->name('segmentations.create');
    Route::get('/segmentations/{id}/edit', [KpiSegmentationController::class, 'edit'])->name('segmentations.edit');
    Route::post('/segmentations', [KpiSegmentationController::class, 'store'])->name('segmentations.store');
    Route::put('/segmentations/{id}', [KpiSegmentationController::class, 'update'])->name('segmentations.update');
    Route::delete('/segmentations/{id}', [KpiSegmentationController::class, 'destroy'])->name('segmentations.destroy');

    // KPI Dimensions CRUD routes
    Route::get('/dimensions/create/{kpi_id}', [KpiDimensionController::class, 'create'])->name('dimensions.create');
    Route::post('/dimensions', [KpiDimensionController::class, 'store'])->name('dimensions.store');
    Route::get('/dimensions/{id}/edit', [KpiDimensionController::class, 'edit'])->name('dimensions.edit');
    Route::put('/dimensions/{id}', [KpiDimensionController::class, 'update'])->name('dimensions.update');
    Route::delete('/dimensions/{id}', [KpiDimensionController::class, 'destroy'])->name('dimensions.destroy');


}); })

?>
