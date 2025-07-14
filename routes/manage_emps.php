<?php
use App\Http\Controllers\Admin\AdminPendingController;
use App\Http\Controllers\IssueCertController;
use App\Http\Controllers\LoadsController;
use App\Http\Controllers\LoadsImportController;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\ManageCertificateController;
use App\Http\Controllers\SharepointController;
use App\Models\certifications;
use App\Models\Departments;
use App\Models\Employee;
use App\Models\HAUCert;
use App\Models\Loads;
use App\Models\LoadsImport;
use App\Models\Subjects;
use App\Models\tags;
use App\Models\temp_subjects;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['admin','revalidate'])->group(function() { 

        /************** PENDING REVIEWS *****************/

        ////view certifications

        Route::get('manage-emps/pendings', [AdminPendingController::class, 'loadPending'])->name('admin.pendings');

        //view item
        Route::get('manage-emps/review/{id}',[AdminPendingController::class, 'reviewItem'])->name('admin.pendings.view'); 

        //search bar
        Route::get('manage-emps/pendings/search',[AdminPendingController::class, 'search'])-> name('admin.pendings.search');
        Route::get('manage-emps/pendings/user',[PendingController::class, 'search'])-> name('admin.pendings.search2');

        Route::get('manage-emps/pendings/search/id/',[AdminPendingController::class, 'loadSearch'])-> name('admin.pendings.loadsearch');

        //approving items
        Route::patch('manage-emps/pendings/{id}/approve', [AdminPendingController::class, 'approveItem'])->name('admin.pendings.approved'); 

        //toreview items
        Route::patch('manage-emps/pendings/{id}/toreview', [AdminPendingController::class, 'toreviewItem'])-> name('admin.pendings.toreview'); 

         /*************** TEACHING LOADS ****************/
        Route::get('manage-emps/teaching-loads', function() {
            return view('manage-emps.loads.main');
        })->name('admin.loads.db');

        Route::get('manage-emps/teaching-loads/user', function() { 
            return view('manage-emps.loads.loads');  
        })->name('admin.loads'); 

        Route::get('manage-emps/teaching-loads/search', function() { 
            $loads = Loads::latest()->where('added_by', Auth::user()->id)->take(10)->get(); 
            return view('manage-emps.loads.search')->with(['loads'=> $loads]); 
        })->name('admin.loads.search');

        Route::get('manage-emps/teaching-loads/search/user', [LoadsController::class, 'load_search'])->name('admin.loads.user.search'); 

        Route::get('manage-emps/teaching-loads/batch', function() { 
            $admin_dept = Employee::where('emp_id', Auth::user()->id)->first()->emp_dept;
            $admin_role = Auth::user()->role;
            return view('manage-emps.loads.batch')->with(['subj' => null, 'msg'=> '', 'semesters' => tags::where('category', 'semester')->get(), 'depts' => Departments::orderBy('dept', 'asc')->get(), 'admin_dept' => $admin_dept, 'admin_role' => $admin_role]); 
        })->name('admin.loads.batch'); 

        Route::get('manage-emps/teaching-loads/batch/s', [LoadsController::class, 'load_subject'])->name('admin.loads.batch.subj'); 
        Route::get('manage-emps/teaching-loads/batch/{id}/add-users', [LoadsController::class,'load_add'])->name('admin.loads.batch.users'); 
        Route::get('manage-emps/teaching-loads/load-user',[LoadsController::class, 'loaduser'])->name ('admin.loads.user');
        Route::get('manage-emps/teaching-loads/add/load-subj',[LoadsController::class, 'loadsubj'])->name ('admin.loads.subj');
        Route::get('manage-emps/teaching-loads/add', [LoadsController::class, 'add'])->name('admin.loads.add'); 
        Route::get('manage-emps/teaching-loads/loads/', [LoadsController::class, 'loadshow'])->name('admin.lbs');
        Route::get('manage-emps/teaching-loads/loads/{id}', [LoadsController::class, 'loadlbs'])->name('admin.lbs.view');
        Route::get('manage-emps/teaching-loads/loads/by-subject', [LoadsController::class, 'loadsBySubject'])->name('admin.loads.bySubject');
        Route::delete('/manage-emps/loads/{id}', [LoadsController::class, 'destroy'])->name('admin.loads.destroy');

        Route::delete('manage-emps/teaching-loads/search/delete/{id}', [LoadsController::class, 'destroySearch'])->name('admin.loads.search.delete');

        Route::post('manage-emps/teaching-loads/add/load', [LoadsController::class, 'store'])->name('admin.loads.store'); 
        Route::delete('manage-emps/teaching-loads/delete/{id}', [LoadsController::class, 'destroy'])->name('admin.loads.delete');

        ///BATCH UPLOAD - QUEUEING SYSTEM
        Route::post('manage-emps/teaching-loads/batch/add-users', [LoadsController::class, 'addToQueue'])->name('admin.queue.add'); 
        Route::delete('manage-emps/teaching-loads/batch/add-users', [LoadsController::class, 'removeQueue'])->name('admin.queue.remove'); 

        Route::post('manage-emps/teaching-loads/batch-upload', [LoadsController::class, 'batchUpload'])->name('admin.queue.upload'); 

        //////////IMPORTS/UPLOADING LOADS FEATURE
        Route::get('manage-emps/teaching-loads/import', function() { 
            return view('manage-emps.loads.import.upload')->with(['imported'=> false]); 
        })->name('admin.loads.upload'); 
        Route::get('manage-emps/teaching-loads/imports', function() { 
            return view('manage-emps.loads.import.imports')->with([
                'loads'=> LoadsImport::all()
            ]); 
        })->name('admin.loads.imports'); 
        Route::post('manage-emps/teaching-loads/import', [LoadsImportController::class, 'import_file'])->name('admin.loads.import'); 
        Route::post('manage-emps/teaching-loads/import/save', [LoadsImportController::class, 'upload_file'])->name('admin.loads.import.save'); 

    /************** SUBJECTS  *****************/
    Route::get('manage-emps/subjects', [SubjectsController::class, 'index'])->name('admin.subjects'); 

    Route::get('manage-emps/subjects/search_item', [SubjectsController::class, 'search'])->name('admin.subjects.search2');

    Route::get('manage-emps/subjects/view-subject', [SubjectsController::class, 'view'])->name('admin.subjects.view');
    Route::get('manage-emps/subjects/add-subject',[SubjectsController::class, 'add'])->name('admin.subjects.add'); 

    Route::get('manage-emps/subjects/delete', function() { 
        return view('manage-emps.subjects.delete')->with([ 
            'subjects' => Subjects::orderBy('subj_title', 'asc') -> paginate(10)
        ]);
    })->name('admin.subjects.delete.view');

    Route::get('manage-emps/subjects/delete-partial', [SubjectsController::class, 'deletePartial'])
     ->name('admin.subjects.deletePartial');

    Route::get('manage-emps/subjects/load/{id}', [SubjectsController::class, 'load'])->name('admin.subjects.load');

    Route::get('manage-emps/subjects/load/{subj}/s',[SubjectsController::class, 'loadSearch'])->name('admin.subjects.loadsearch');

    Route::post('manage-emps/subjects/loadtouser', [SubjectsController::class, 'load_to_user'])->name('admin.subjects.loadtouser');

    Route::delete('manage-emps/subjects/destroy/{id}', [SubjectsController::class, 'destroy'])->name('admin.subjects.destroy');

    Route::delete('manage-emps/subjects/delete', [SubjectsController::class, 'delete'])->name('admin.subjects.delete');

    Route::post('manage-emps/subjects/add-subject/save', [SubjectsController::class, 'store'])->name('admin.subjects.create'); 

    Route::get('manage-emps/subjects/upload', function() { 
        return view('manage-emps.subjects.upload')->with(['imported'=>false]); 
    })->name('admin.subjects.upload'); 

    Route::get('manage-emps/subjects/upload/imports', function() { 
        return view('manage-emps.subjects.popup')->with([
            'subjects' => temp_subjects::paginate(10)
        ]);
    })->name('admin.subjects.popup');

    Route::post('manage-emps/subjects/upload/load',[SubjectsController::class, 'import_file'])->name('admin.subjects.import'); 
    Route::post('manage-emps/subjets/upload/save',[SubjectsController::class, 'load_file'])->name('admin.subjects.save');

    Route::put('manage-emps/subjects/update/{id}', [SubjectsController::class, 'update'])->name('admin.subjects.update');

    /******************** ISSUANCE OF CERTIFICATES ********************/
    Route::get('manage-emps/certifications', function() { 
        $msg = '';
        if(session('msg')=='Batch issue')  { 
                $msg = 'Successfully issued certificates for all selected users!' ; 
        } 

        if(Auth::user()->role == 'Dean') { 
            $certs = HAUCert::where('issued_by', Auth::user()->user->department->dept)->get(); 
        } else { 
            $certs = HAUCert::where('created_by', Auth::user()->id)->get(); 
        }
        return view('manage-emps.certs.dashboard')->with([ 
            'msg'=> $msg,
            'certs'=> $certs, 
            'depts'=> Departments::orderBy('dept', 'asc')->get()
        ]); 
    })->name('admin.certs'); 

    Route::get('manage-emps/certifications/issue/{id}', [IssueCertController::class, 'load_issue'])->name('admin.certs.load');

    Route::get('manage-emps/certifications/view/{id}', function($id) { 
        return view('manage-emps.certs.view')->with([ 
            'data'=>HAUCert::where('id',$id)->first(), 
            'certs'=> certifications::where('hau_cert', $id)->get() 
        ]);
    })->name('admin.certs.view'); 

    Route::post('manage-emps/certifications/create', [IssueCertController::class, 'create_certification'])->name('admin.certs.create'); 

    Route::post('manage-emps/certifications/issue/{id}/p', [IssueCertController::class, 'issue_cert'])->name('admin.certs.issue');

    // Edit Certificate - Show the edit form
    Route::get('manage-emps/certifications/edit/{id}', [IssueCertController::class, 'edit'])->name('admin.certs.edit');
    // Update Certificate - Submit the edit form
    Route::put('manage-emps/certifications/update/{id}', [IssueCertController::class, 'update'])->name('admin.certs.update');


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
    });
?> 
