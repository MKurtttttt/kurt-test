<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\certifications;
use App\Models\Departments;
use App\Models\dependencies;
use App\Models\Employee;
use App\Models\Employee_Login;
use App\Models\Education;
use App\Models\Employment;
use App\Models\HiringHistory;
use App\Models\HiringInfo;
use App\Models\Licenses;
use App\Models\orgs;
use App\Models\provincial_contact;
use App\Models\Publication;
use App\Models\Research;
use App\Models\PublicationCoauthor;
use App\Models\ResearchCoauthor;
use App\Models\tags;
use App\Models\Trainings;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; 
use App\Models\TempUser; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Imports\UserRecordsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class UserRecordsController extends Controller
{
    // Updated Search Function
    public function search(Request $request) 
    {
        $query = $request->get('query', '');

        switch(Auth::user()->role) {
            case 'SuperAdmin':
            case 'HR Admin':
                $data = Employee::with('login')
                    ->where(function($q) use ($query) {
                        $q->where('emp_id', 'LIKE', "%{$query}%")
                          ->orWhere('emp_fname', 'LIKE', "%{$query}%")
                          ->orWhere('emp_mname', 'LIKE', "%{$query}%")
                          ->orWhere('emp_lname', 'LIKE', "%{$query}%")
                          ->orWhereRaw("CONCAT_WS(' ', emp_fname, NULLIF(emp_mname, ''), emp_lname) LIKE ?", ["%{$query}%"])
                          ->orWhereRaw("CONCAT_WS(' ', emp_lname, emp_fname, NULLIF(emp_mname, '')) LIKE ?", ["%{$query}%"]);


                    })
                    ->orderBy('emp_lname','asc')
                    ->paginate(6);
                break;

            default:
                $data = Employee::with('login')
                    ->where('emp_dept', Auth::user()->emp_dept)
                    ->where(function($q) use ($query) {
                        $q->where('emp_id', 'LIKE', "%{$query}%")
                          ->orWhere('emp_fname', 'LIKE', "%{$query}%")
                          ->orWhere('emp_mname', 'LIKE', "%{$query}%")
                          ->orWhere('emp_lname', 'LIKE', "%{$query}%")
                          ->orWhereRaw("CONCAT_WS(' ', emp_fname, NULLIF(emp_mname, ''), emp_lname) LIKE ?", ["%{$query}%"])
                          ->orWhereRaw("CONCAT_WS(' ', emp_lname, emp_fname, NULLIF(emp_mname, '')) LIKE ?", ["%{$query}%"]);


                    })
                    ->orderBy('emp_lname','asc')
                    ->paginate(6);
                break;
        }

        $data->setPath(route('admin.users.search'));
        $paginationLinks = $data->appends(['query' => $query])->links()->render();

        return response()->json([
            'users'         => $data->items(),
            'count'         => $data->total(),
            'links'         => $paginationLinks,
            'next_page_url' => $data->nextPageUrl(),
            'prev_page_url' => $data->previousPageUrl(),
        ]);
    }

    protected function fetch()
    {
        switch(Auth::user()->role) {
            case 'SuperAdmin':
            case 'HR Admin':
                $data = Employee::with('login')
                    ->orderBy('emp_lname', 'asc')
                    ->paginate(6);
                $count = Employee::count();
                break;

            default:
                $data = Employee::with('login')
                    ->where('emp_dept', Auth::user()->emp_dept)
                    ->orderBy('emp_lname', 'asc')
                    ->paginate(6);
                $count = Employee::where('emp_dept', Auth::user()->emp_dept)->count();
                break;
        }

        return [$data, $count];
    }

    public function filter($type, Request $request)
    {
        if ($type == 'multi') {
            $positions = $request->positions;
            $natures   = $request->natures;
            $tenures   = $request->tenures;
            $license   = $request->license;
            $status    = $request->status;
            $selectedDept = $request->dept;
            $selectedRole = $request->role;

            // Retrieve the new educational attainment filter from the request
            $education = $request->education;
            $educationSelected = $education ?: [];

            $users = Employee::with('login','hiring','educations')
                ->when(!in_array(Auth::user()->role, ['SuperAdmin','HR Admin']), function($q){
                    $q->where('emp_dept', Auth::user()->emp_dept);
                })
                ->when($selectedDept, function($q) use ($selectedDept) {
                    $q->where('emp_dept', $selectedDept);
                })
                ->when($selectedRole, function($q) use ($selectedRole) {
                    $q->whereHas('login', function($sub) use ($selectedRole) {
                        $sub->where('role', $selectedRole);
                    });
                })
                ->when($positions, function($q) use ($positions) {
                    $q->whereHas('hiring', function($sub) use ($positions) {
                        $sub->whereIn('emp_position', $positions);
                    });
                })
                ->when($natures, function($q) use ($natures) {
                    $q->whereHas('hiring', function($sub) use ($natures) {
                        $sub->whereIn('emp_nature', $natures);
                    });
                })
                ->when($tenures, function($q) use ($tenures) {
                    $q->whereHas('hiring', function($sub) use ($tenures) {
                        $sub->whereIn('emp_tenure', $tenures);
                    });
                })
                ->when($license, function($q) use ($license) {
                    $q->whereHas('hiring', function($sub) use ($license) {
                        $sub->whereIn('license', $license);
                    });
                })
                ->when($status, function($q) use ($status) {
                    $q->whereHas('login', function($sub) use ($status) {
                        $sub->whereIn('terminated', $status);
                    });
                })
                ->when($education, function($q) use ($education) {
                    $q->whereHas('educations', function($sub) use ($education) {
                        $sub->whereIn('level', $education);
                    });
                })
                ->orderBy('emp_lname','asc')
                ->paginate(6);

            // Compute chart data based on filters
            $positionsSelected = $positions ?: [];
            $naturesSelected = $natures ?: [];
            $tenuresSelected = $tenures ?: [];
            $licenseSelected = $license ?: [];
            $statusSelected = $status ?: [];

            $query = Employee::with('login', 'hiring', 'educations')->orderBy('emp_lname', 'asc');
            if($selectedDept){
                $query->where('emp_dept', $selectedDept);
            }
            if($selectedRole){
                $query->whereHas('login', function($sub) use ($selectedRole) {
                    $sub->where('role', $selectedRole);
                });
            }
            if(count($positionsSelected) > 0){
                $query->whereHas('hiring', function($sub) use ($positionsSelected) {
                    $sub->whereIn('emp_position', $positionsSelected);
                });
            }
            if(count($naturesSelected) > 0){
                $query->whereHas('hiring', function($sub) use ($naturesSelected) {
                    $sub->whereIn('emp_nature', $naturesSelected);
                });
            }
            if(count($tenuresSelected) > 0){
                $query->whereHas('hiring', function($sub) use ($tenuresSelected) {
                    $sub->whereIn('emp_tenure', $tenuresSelected);
                });
            }
            if(count($licenseSelected) > 0){
                $query->whereHas('hiring', function($sub) use ($licenseSelected) {
                    $sub->whereIn('license', $licenseSelected);
                });
            }
            if(count($statusSelected) > 0){
                $query->whereHas('login', function($sub) use ($statusSelected) {
                    $sub->whereIn('terminated', $statusSelected);
                });
            }
            if(count($educationSelected) > 0){
                $query->whereHas('educations', function($sub) use ($educationSelected) {
                    $sub->whereIn('level', $educationSelected);
                });
            }
            if(!in_array(Auth::user()->role, ['SuperAdmin','HR Admin'])){
                $query->where('emp_dept', Auth::user()->emp_dept);
            }
            $allFiltered = $query->get();
            $grouped = $allFiltered->groupBy(function($item) use ($positionsSelected, $naturesSelected, $tenuresSelected, $licenseSelected, $statusSelected, $educationSelected) {
                $groupingLabel = $item->emp_dept . ', ' . ($item->login->role ?? 'Unknown');
                if (!empty($positionsSelected)) {
                    $groupingLabel .= ', ' . ($item->hiring->emp_position ?? 'Unknown');
                }
                if (!empty($naturesSelected)) {
                    $groupingLabel .= ', ' . ($item->hiring->emp_nature ?? 'Unknown');
                }
                if (!empty($tenuresSelected)) {
                    $groupingLabel .= ', ' . ($item->hiring->emp_tenure ?? 'Unknown');
                }
                if (!empty($licenseSelected)) {
                    $groupingLabel .= ', ' . (isset($item->hiring->license)
                        ? ($item->hiring->license == 1 ? 'License Required' : 'Not Required')
                        : 'Unknown');
                }
                if (!empty($statusSelected)) {
                    $groupingLabel .= ', ' . (isset($item->login->terminated)
                        ? ($item->login->terminated == 0 ? 'Active' : 'Terminated')
                        : 'Unknown');
                }
                if (!empty($educationSelected)) {
                    $groupingLabel .= ', ' . ($item->educations->first()->level ?? 'Unknown');
                }
                return $groupingLabel;
            });
            $chartDataArray = $grouped->map(function($group) {
                return $group->count();
            })->toArray();
            $totalEmployees = array_sum(array_values($chartDataArray));

            // If the request expects JSON (AJAX), return a JSON response with rendered partials and chart data
            if(request()->expectsJson()){
                $userListHtml = view('admin.records.users.partials.user-list', ['users' => $users])->render();
                $paginationHtml = view('admin.records.users.partials.pagination', ['users' => $users])->render();
                return response()->json([
                    'userListHtml'   => $userListHtml,
                    'paginationHtml' => $paginationHtml,
                    'userCount'      => $users->total(),
                    'chartLabels'    => array_keys($chartDataArray),
                    'chartData'      => array_values($chartDataArray),
                    'totalEmployees' => $totalEmployees
                ]);
            }

            // Fallback: return full view (non-AJAX)
            return view('admin.records.users.all')->with([
                'users'             => $users,
                'count'             => $users->total(),
                'depts'             => \App\Models\Departments::orderBy('dept','asc')->get(),
                'positionsSelected' => $positions ?: [],
                'naturesSelected'   => $natures   ?: [],
                'tenuresSelected'   => $tenures   ?: [],
                'licenseSelected'   => $license   ?: [],
                'statusSelected'    => $status    ?: [],
                'deptSelected'      => $selectedDept,
                'roleSelected'      => $selectedRole,
                'educationSelected' => $educationSelected,
                'filter_type'       => 'multi',
            ]);
        }
    }

    public function index()
    {
        $data = $this->fetch();
        $depts = Departments::orderBy('dept', 'asc')->get();

        return view('admin.records.users.all')->with([
            'users' => $data[0],
            'count' => $data[1],
            'depts' => $depts
        ]);
    }

    public function view_user($id)
    {
        $data = Employee::with([
            'department',
            'login',
            'provincial_contact',
            'emergency_contact',
            'accounting_details',
            'hiring',
            'hiringHistory',
            'certification',   
            'trainings',       
            'licenses',        
            'educations',      
            'employments',     
            'research',        
            'publication',     
            'orgs',            
            'dependencies',    
        ])->where('emp_id', $id)->first();

        $hauCerts = certifications::where('emp_id', $id)
                ->whereNotNull('hau_cert')
                ->get();
        
        $trainings = Trainings::where('emp_id', $id)
               ->get();
        
        $combinedTrainings = $hauCerts->merge($trainings);

        $hasdep = Departments::where('code', $data->emp_dept)->exists();

        return view('admin.records.users.view-user')->with([
            'dep' => $hasdep,
            'data' => $data,
            'trainings' => $combinedTrainings
        ]);
    }
    
    public function viewItem($id)
    {
        $item = certifications::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            return view('admin.records.users.view-item', compact('item', 'user'));
        }

        $item = Trainings::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            return view('admin.records.users.view-item', compact('item', 'user'));
        }

        $item = Licenses::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            return view('admin.records.users.view-item', compact('item', 'user'));
        }

        $item = Education::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            return view('admin.records.users.view-item', compact('item', 'user'));
        }

        $item = Employment::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            return view('admin.records.users.view-item', compact('item', 'user'));
        }

        $item = Research::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            $coauthors = $item->coauthors;
            return view('admin.records.users.view-item', [
                'item'      => $item,
                'user'      => $user,
                'coauthors' => $coauthors,
            ]);
        }

        $item = Publication::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            $coauthors = $item->coauthors;
            return view('admin.records.users.view-item', [
                'item'      => $item,
                'user'      => $user,
                'coauthors' => $coauthors,
            ]);
        }

        $item = orgs::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            return view('admin.records.users.view-item', compact('item', 'user'));
        }

        $item = dependencies::find($id);
        if ($item) {
            $user = Employee::where('emp_id', $item->emp_id)->first();
            return view('admin.records.users.view-item', compact('item', 'user'));
        }

        abort(404, 'Record not found.');
    }

    public function edit_user($id, $section = null)
    {
        $data = Employee::where('emp_id', $id)->with([
            'department','provincial_contact','emergency_contact','accounting_details'
        ])->first();

        $dep = dependencies::where('emp_id',$id)->get();
        $hasdep = Departments::where('code', $data->emp_dept)->exists();

        // Simply pass $section to the view
        return view('admin.records.users.edit-info')->with([
            'dep'=> $hasdep,
            'data'=> $data,
            'dependencies'=> $dep,
            'section' => $section   // Pass the section so blade can decide which UI to show
        ]);
    }

    public function update_user(Request $request, $id, $section = null)
    {
        $employee = Employee::where('emp_id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            switch($section)
            {
                case 'personal':
                    // Example updates for "Personal Data" only
                    $request->validate([
                        'emp_fname'=>'required|string|max:255',
                        'emp_mname' => 'nullable|string|max:255',
                        'emp_lname'=>'required|string|max:255',
                        'emp_gender' => 'nullable|string|max:50',
                        'emp_dob' => 'nullable|date',
                        'emp_pob' => 'nullable|string|max:255',
                        'emp_cStatus' => 'nullable|string|max:255',
                        'emp_religion' => 'nullable|string|max:255',
                        'emp_blood_type' => 'nullable|string|max:10',
                    ]);
                    $employee->update([
                        'emp_fname' => $request->emp_fname,
                        'emp_mname' => $request->emp_mname,
                        'emp_lname' => $request->emp_lname,
                        'emp_gender'=> $request->emp_gender,
                        'emp_dob'   => $request->emp_dob,
                        'emp_pob'   => $request->emp_pob,
                        'emp_cStatus' => $request->emp_cStatus,
                        'emp_religion' => $request->emp_religion,
                        'emp_blood_type' => $request->emp_blood_type,
                    ]);
                    break;

                case 'contact':
                    $request->validate([
                        'emp_houseno'=>'nullable|string|max:50',
                        'street'=>'nullable|string|max:50',
                        'city'=>'nullable|string|max:50',
                        'province' => 'nullable|string|max:50',
                        'postal_code' => 'nullable|string|max:20',
                        'home_phone' => 'nullable|string|max:20',
                        'mobile_phone' => 'nullable|string|max:20',
                        'email_address_1' => 'nullable|email|max:255',
                        'email_address_2' => 'nullable|email|max:255',
                    ]);
                    $employee->update([
                        'emp_houseno'   => $request->emp_houseno,
                        'street'        => $request->street,
                        'brgy'          => $request->brgy,
                        'city'          => $request->city,
                        'province'      => $request->province,
                        'postal_code'   => $request->postal_code,
                        'home_phone'    => $request->home_phone,
                        'mobile_phone'  => $request->mobile_phone,
                        'email_address_1'=> $request->email_address_1,
                        'email_address_2'=> $request->email_address_2,
                    ]);
                    break;

                case 'provincial':
                    $request->validate([
                        'pc_emp_houseno'=>'nullable|string|max:50',
                        'pc_street' => 'nullable|string|max:50',
                        'pc_brgy' => 'nullable|string|max:50',
                        'pc_city' => 'nullable|string|max:50',
                        'pc_province' => 'nullable|string|max:50',
                        'pc_postal_code' => 'nullable|string|max:20',
                        'pc_phone' => 'nullable|string|max:20',
                    ]);
                    $provincial = provincial_contact::where('id', $id)->first();
                    if (!$provincial) {
                        $provincial = new provincial_contact;
                        $provincial->id = $id;
                    }
                    $provincial->pc_emp_houseno = $request->pc_emp_houseno;
                    $provincial->pc_street      = $request->pc_street;
                    $provincial->pc_brgy        = $request->pc_brgy;
                    $provincial->pc_city        = $request->pc_city;
                    $provincial->pc_province    = $request->pc_province;
                    $provincial->pc_postal_code = $request->pc_postal_code;
                    $provincial->pc_phone       = $request->pc_phone;
                    $provincial->save();
                    break;

                case 'emergency':
                    $request->validate([
                        'cp_fname'=>'nullable|string|max:255',
                        'cp_mname' => 'nullable|string|max:255',
                        'cp_lname' => 'nullable|string|max:255',
                        'cp_relationship' => 'nullable|string|max:255',
                        'cp_house_no' => 'nullable|string|max:50',
                        'cp_street' => 'nullable|string|max:50',
                        'cp_brgy' => 'nullable|string|max:50',
                        'cp_city' => 'nullable|string|max:50',
                        'cp_province' => 'nullable|string|max:50',
                        'cp_postal_code' => 'nullable|string|max:20',
                        'cp_home_phone' => 'nullable|string|max:20',
                        'cp_mobile_no' => 'nullable|string|max:20',
                    ]);
                    $em = \App\Models\emergency::where('emp_id', $id)->first();
                    if (!$em) {
                        $em = new \App\Models\emergency;
                        $em->emp_id = $id;
                    }
                    $em->cp_fname       = $request->cp_fname;
                    $em->cp_mname       = $request->cp_mname;
                    $em->cp_lname       = $request->cp_lname;
                    $em->cp_relationship= $request->cp_relationship;
                    $em->cp_house_no    = $request->cp_house_no;
                    $em->cp_street      = $request->cp_street;
                    $em->cp_city        = $request->cp_city;
                    $em->cp_brgy        = $request->cp_brgy;
                    $em->cp_province    = $request->cp_province;
                    $em->cp_postal_code = $request->cp_postal_code;
                    $em->cp_home_phone  = $request->cp_home_phone;
                    $em->cp_mobile_no   = $request->cp_mobile_no;
                    $em->save();
                    break;

                case 'accounting':
                    $request->validate([
                        'sss_no'=>'nullable|string|max:50',
                        'tax_no' => 'nullable|string|max:50',
                        'pagibig_no' => 'nullable|string|max:50',
                        'philhealth_no' => 'nullable|string|max:50',
                    ]);
                    $acc_details = $employee->accounting_details;
                    if (!$acc_details) {
                        $acc_details = new \App\Models\acc_details;
                        $acc_details->emp_id = $id;
                    }
                    $acc_details->sss_no        = $request->sss_no;
                    $acc_details->tax_no        = $request->tax_no;
                    $acc_details->pagibig_no    = $request->pagibig_no;
                    $acc_details->philhealth_no = $request->philhealth_no;
                    $acc_details->save();
                    break;
                
                case 'login':
                    $request->validate([
                        'email' => 'required|email|max:255|unique:tbl_login,email,'.$employee->emp_id.',id',
                        'role'  => 'required|string|in:Employee,SuperAdmin,HR Admin,Dean,ScholarManager,IDC Admin,ISO Document Handler',
                        // The password is optional. If given, it must be confirmed.
                        'password' => 'nullable|string|min:6|confirmed',
                    ]);
        
                    $login = $employee->login;
                    if (!$login) {
                        // In case no login record exists, create one.
                        $login = new Employee_Login;
                        $login->id = $employee->emp_id;
                    }
                    $login->email = $request->email;
                    if($request->filled('password')) {
                        $login->password = Hash::make($request->password);
                    }
                    $login->role = $request->role;
                    $login->save();
                    break;

                default:
                    // If no valid section is specified, do nothing or handle old form updates
                    break;
            }

            DB::commit();
            return redirect()
                ->route('admin.users.view', $id)
                ->with('msg', ucfirst($section).' info updated successfully.');
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Log::error('Error updating user info: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while updating.']);
        }
    }
    
    public function add($origin = null)
    {
        return view('admin.records.users.add-user', compact('origin'))->with([
            'dept' => Departments::orderBy('dept', 'asc')->get(),
            'gender' => tags::where('category', 'gender')->get(),
            'civil_status' => tags::where('category', 'civil_status')->get(),
            'position' => tags::where('category', 'emp_category')->get(),
            'nature' => tags::where('category', 'emp_status')->get(),
            'nontenured' => tags::where('category', 'non_tenured')->get(),
            'tenure' => tags::where('category', 'tenure')->get(),
            'roles' => tags::where('category', 'roles')->get()
        ]);
    }

    public function store(Request $request)
    {
        // Define roles that only require limited information
        $rolesWithLimitedInfo = ['SuperAdmin', 'HR Admin' , 'Dean' , 'IDC Admin' , 'ISO Document Handler'];
        
        // Check if the selected role requires limited fields
        $requiresLimitedInfo = in_array($request->role, $rolesWithLimitedInfo);
        
        // Base validation rules (always required)
        $validationRules = [
            'emp_id' => 'required|unique:tbl_info,emp_id',
            'emp_fname' => 'required|string|max:255',
            'emp_mname' => 'nullable|string|max:255',
            'emp_lname' => 'required|string|max:255',
            'emp_dept' => 'required|string|max:255',
            'emp_gender' => 'nullable|string|max:50',
            'emp_maiden_name' => 'nullable|string|max:255',
            'emp_dob' => ['required', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],
            'emp_pob' => 'required|string|max:255',
            'emp_cStatus' => 'nullable|string|max:255',
            'emp_religion' => 'nullable|string|max:255',
            'emp_blood_type' => 'nullable|string|max:10',
            'email' => 'required|email|unique:tbl_login,email',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string|same:password',
            'role' => 'required|string|in:Employee,SuperAdmin,HR Admin,Dean,IDC Admin,ISO Document Handler',
        ];
        
        // Conditional validation rules (only required for certain roles)
        if (!$requiresLimitedInfo) {
            $validationRules = array_merge($validationRules, [
                // Contact Information
                'emp_houseno' => 'required|string|max:50',
                'street' => 'required|string|max:50',
                'brgy' => 'required|string|max:50',
                'city' => 'required|string|max:50',
                'province' => 'required|string|max:50',
                'postal_code' => 'required|string|max:20',
                'home_phone' => 'required|string|max:20',
                'mobile_phone' => 'required|string|max:20',
                'email_address_1' => 'required|email|max:255',
                'email_address_2' => 'required|email|max:255',
                
                // Provincial Contact Information
                'pc_emp_houseno' => 'nullable|string|max:50',
                'pc_street' => 'nullable|string|max:50',
                'pc_brgy' => 'nullable|string|max:50',
                'pc_city' => 'nullable|string|max:50',
                'pc_province' => 'nullable|string|max:50',
                'pc_postal_code' => 'nullable|string|max:20',
                'pc_phone' => 'nullable|string|max:20',
                
                // Emergency Contact Information
                'cp_fname' => 'required|string|max:255',
                'cp_mname' => 'nullable|string|max:255',
                'cp_lname' => 'required|string|max:255',
                'cp_relationship' => 'required|string|max:255',
                'cp_house_no' => 'required|string|max:50',
                'cp_street' => 'required|string|max:50',
                'cp_city' => 'required|string|max:50',
                'cp_province' => 'required|string|max:50',
                'cp_postal_code' => 'required|string|max:20',
                'cp_home_phone' => 'required|string|max:20',
                'cp_mobile_no' => 'required|string|max:20',
                
                // Accounting Details
                'sss_no' => 'nullable|string|max:50',
                'tax_no' => 'nullable|string|max:50',
                'pagibig_no' => 'nullable|string|max:50',
                'philhealth_no' => 'nullable|string|max:50',
                
                // Hiring Information
                'date_hired' => 'required|date',
                'position' => 'required|string|max:50',
                'nature' => 'required|string|max:50',
                'tenure' => 'required|string|max:50',
                'nontenured' => 'required_if:tenure,Non-tenured|string|max:50',
                'license' => 'required|boolean',
            ]);
        } else {
            $validationRules = array_merge($validationRules, [
                // Contact Information
                'emp_houseno' => 'nullable|string|max:50',
                'street' => 'nullable|string|max:50',
                'brgy' => 'nullable|string|max:50',
                'city' => 'nullable|string|max:50',
                'province' => 'nullable|string|max:50',
                'postal_code' => 'nullable|string|max:20',
                'home_phone' => 'nullable|string|max:20',
                'mobile_phone' => 'nullable|string|max:20',
                'email_address_1' => 'nullable|email|max:255',
                'email_address_2' => 'nullable|email|max:255',
                
                // Provincial Contact Information
                'pc_emp_houseno' => 'nullable|string|max:50',
                'pc_street' => 'nullable|string|max:50',
                'pc_brgy' => 'nullable|string|max:50',
                'pc_city' => 'nullable|string|max:50',
                'pc_province' => 'nullable|string|max:50',
                'pc_postal_code' => 'nullable|string|max:20',
                'pc_phone' => 'nullable|string|max:20',
                
                // Emergency Contact Information
                'cp_fname' => 'nullable|string|max:255',
                'cp_mname' => 'nullable|string|max:255',
                'cp_lname' => 'nullable|string|max:255',
                'cp_relationship' => 'nullable|string|max:255',
                'cp_house_no' => 'nullable|string|max:50',
                'cp_street' => 'nullable|string|max:50',
                'cp_city' => 'nullable|string|max:50',
                'cp_province' => 'nullable|string|max:50',
                'cp_postal_code' => 'nullable|string|max:20',
                'cp_home_phone' => 'nullable|string|max:20',
                'cp_mobile_no' => 'nullable|string|max:20',
                
                // Accounting Details
                'sss_no' => 'nullable|string|max:50',
                'tax_no' => 'nullable|string|max:50',
                'pagibig_no' => 'nullable|string|max:50',
                'philhealth_no' => 'nullable|string|max:50',
                
                // Hiring Information
                'date_hired' => 'nullable|date',
                'position' => 'nullable|string|max:50',
                'nature' => 'nullable|string|max:50',
                'tenure' => 'nullable|string|max:50',
                'nontenured' => 'nullable|string|max:50',
                'license' => 'nullable|boolean',
            ]);
        }
        
        $validatedData = $request->validate($validationRules);

        try {
            DB::beginTransaction();

            // Create Employee record
            Employee::create([
                'emp_id' => $request->emp_id,
                'emp_fname' => $request->emp_fname,
                'emp_mname' => $request->emp_mname,
                'emp_lname' => $request->emp_lname,
                'emp_dept' => $request->emp_dept,
                'emp_gender' => $request->emp_gender,
                'emp_maiden_name' => $request->emp_maiden_name,
                'emp_dob' => $request->emp_dob,
                'emp_pob' => $request->emp_pob,
                'emp_cStatus' => $request->emp_cStatus,
                'emp_religion' => $request->emp_religion,
                'emp_blood_type' => $request->emp_blood_type,
                'emp_houseno' => $request->emp_houseno ?? null,
                'street' => $request->street ?? null,
                'brgy' => $request->brgy ?? null,
                'city' => $request->city ?? null,
                'province' => $request->province ?? null,
                'postal_code' => $request->postal_code ?? null,
                'info_status' => 'Active',
                'home_phone' => $request->home_phone ?? null,
                'mobile_phone' => $request->mobile_phone ?? null,
                'email_address_1' => $request->email_address_1 ?? null,
                'email_address_2' => $request->email_address_2 ?? null,
            ]);

            // Create Login record
            Employee_Login::create([
                'id' => $request->emp_id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'terminated' => 0,
            ]);

            if (!$requiresLimitedInfo) {
                // Provincial Contact
                provincial_contact::create([
                    'id' => $request->emp_id,
                    'pc_emp_houseno' => $request->pc_emp_houseno,
                    'pc_street' => $request->pc_street,
                    'pc_brgy' => $request->pc_brgy,
                    'pc_city' => $request->pc_city,
                    'pc_province' => $request->pc_province,
                    'pc_postal_code' => $request->pc_postal_code,
                    'pc_phone' => $request->pc_phone,
                ]);

                // Emergency Contact
                DB::table('tbl_emergency')->insert([
                    'emp_id' => $request->emp_id,
                    'cp_fname' => $request->cp_fname,
                    'cp_mname' => $request->cp_mname,
                    'cp_lname' => $request->cp_lname,
                    'cp_relationship' => $request->cp_relationship,
                    'cp_house_no' => $request->cp_house_no,
                    'cp_street' => $request->cp_street,
                    'cp_city' => $request->cp_city,
                    'cp_province' => $request->cp_province,
                    'cp_postal_code' => $request->cp_postal_code,
                    'cp_home_phone' => $request->cp_home_phone,
                    'cp_mobile_no' => $request->cp_mobile_no,
                ]);

                // Accounting Details
                DB::table('tbl_accounting_details')->insert([
                    'emp_id' => $request->emp_id,
                    'sss_no' => $request->sss_no,
                    'tax_no' => $request->tax_no,
                    'pagibig_no' => $request->pagibig_no,
                    'philhealth_no' => $request->philhealth_no,
                ]);

                // Hiring Information
                $div = "";
                switch($request->position) {
                    case 'Faculty':
                        $div = 'Academic';
                        break;
                    default:
                        $div = 'Non-academic';
                        break;
                }

                HiringInfo::create([
                    'emp_id' => $request->emp_id,
                    'emp_position' => $request->position,
                    'emp_nature' => $request->nature,
                    'emp_tenure' => $request->tenure,
                    'non_tenured' => $request->nontenured,
                    'division' => $div,
                    'license' => $request->license,
                ]);

                // Hiring History
                do { 
                    $uid = $request->emp_id . '-h-' . Str::random(8); 
                } while (HiringHistory::where('id', $uid)->exists()); 

                $deptName = Departments::where('code', $request->emp_dept)->first();

                HiringHistory::create([
                    'id' => $uid,
                    'emp_id' => $request->emp_id,
                    'date'=> $request->date_hired,
                    'position'=> $request->position,
                    'division'=> $div,
                    'department'=> $deptName->dept,
                    'nature'=> $request->nature
                ]);
            } else {
                provincial_contact::create([
                    'id' => $request->emp_id,
                    'pc_emp_houseno' => null,
                    'pc_street' => null,
                    'pc_brgy' => null,
                    'pc_city' => null,
                    'pc_province' => null,
                    'pc_postal_code' => null,
                    'pc_phone' => null,
                ]);

                DB::table('tbl_emergency')->insert([
                    'emp_id' => $request->emp_id,
                    'cp_fname' => null,
                    'cp_mname' => null,
                    'cp_lname' => null,
                    'cp_relationship' => null,
                    'cp_house_no' => null,
                    'cp_street' => null,
                    'cp_city' => null,
                    'cp_province' => null,
                    'cp_postal_code' => null,
                    'cp_home_phone' => null,
                    'cp_mobile_no' => null,
                ]);

                DB::table('tbl_accounting_details')->insert([
                    'emp_id' => $request->emp_id,
                    'sss_no' => null,
                    'tax_no' => null,
                    'pagibig_no' => null,
                    'philhealth_no' => null,
                ]);

            }

            DB::commit();

            return redirect()->route('admin.users')->with('success', 'User added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while adding the user. Please try again.']);
        }
    }

    public function addMultiple($origin = null)
    {
        return view('admin.records.users.add-multi', compact('origin'));
    }

    public function load_multiple_users(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:10240',
            'origin' => 'nullable|in:all,users'
        ]);

        // Set default origin if not provided
        $origin = $request->input('origin', 'all');

        try {
            // Stores temp file for reference and delete later
            $file = $request->file('file');
            $filePath = $file->store('temp');
            $fullPath = storage_path('app/' . $filePath); 
            
            $import = new UserRecordsImport(true); // validate data only
            Excel::import($import, $fullPath);
            
            $failures = $import->failures();
            
            if (!empty($failures)) {
                $errors = [];
                
                foreach ($failures as $failure) {
                    $row = $failure->row();
                    $attribute = $failure->attribute();
                    $errorsForRow = $failure->errors();
                    
                    foreach ($errorsForRow as $error) {
                        $errors[] = "Row {$row}, {$error}";
                    }
                }
                
                \Storage::delete($filePath);
                return back()->withErrors($errors)->withInput();
            }
            
            // If validation passes, load preview data with row numbers
            $excel = IOFactory::load($fullPath);
            $preview = [];
            
            // Collect all employee IDs from Personal_Info to determine valid rows
            $validRowNumbers = [];
            if ($sheet = $excel->getSheetByName('Personal_Info')) {
                $data = $sheet->toArray();
                for ($i = 7; $i < count($data); $i++) { 
                    // Check if Employee ID exists
                    if (!empty($data[$i][0])) {
                        $validRowNumbers[] = $i + 1;
                    }
                }
            }
            
            // Helper function to check if a row has any data
            $hasData = function($row) {
                foreach ($row as $value) {
                    if ($value !== null && $value !== '') {
                        return true;
                    }
                }
                return false;
            };
            
            // Load Personal_Info
            if ($sheet = $excel->getSheetByName('Personal_Info')) {
                $data = $sheet->toArray();
                $headers = $data[6]; 
                for ($i = 7; $i < count($data); $i++) { 
                    $rowNumber = $i + 1;
                    // Only show rows that have Employee ID
                    if (in_array($rowNumber, $validRowNumbers)) {
                        // Ensure headers and data have same length
                        $rowData = $data[$i];
                        if (count($headers) === count($rowData)) {
                            $combined = array_combine($headers, $rowData);
                            $combined['__row_number__'] = $rowNumber;
                            $preview['personal'][] = $combined;
                        }
                    }
                }
            }
            
            // Load Login_Info
            if ($sheet = $excel->getSheetByName('Login_Info')) {
                $data = $sheet->toArray();
                $headers = $data[6];
                for ($i = 7; $i < count($data); $i++) {
                    $rowNumber = $i + 1;
                    // Only show rows that have Employee ID and have data
                    if (in_array($rowNumber, $validRowNumbers) && $hasData($data[$i])) {
                        $rowData = $data[$i];
                        if (count($headers) === count($rowData)) {
                            $combined = array_combine($headers, $rowData);
                            $combined['__row_number__'] = $rowNumber;
                            $preview['login'][] = $combined;
                        }
                    }
                }
            }
            
            // Load Contact_Info
            if ($sheet = $excel->getSheetByName('Contact_Info')) {
                $data = $sheet->toArray();
                $headers = $data[6];
                for ($i = 7; $i < count($data); $i++) {
                    $rowNumber = $i + 1;
                    // Only show rows that have Employee ID and have data
                    if (in_array($rowNumber, $validRowNumbers) && $hasData($data[$i])) {
                        $rowData = $data[$i];
                        if (count($headers) === count($rowData)) {
                            $combined = array_combine($headers, $rowData);
                            $combined['__row_number__'] = $rowNumber;
                            $preview['contact'][] = $combined;
                        }
                    }
                }
            }
            
            // Load Prov_Contact_Info
            if ($sheet = $excel->getSheetByName('Prov_Contact_Info')) {
                $data = $sheet->toArray();
                $headers = $data[6];
                for ($i = 7; $i < count($data); $i++) {
                    $rowNumber = $i + 1;
                    // Only show rows that have Employee ID and have data
                    if (in_array($rowNumber, $validRowNumbers) && $hasData($data[$i])) {
                        $rowData = $data[$i];
                        if (count($headers) === count($rowData)) {
                            $combined = array_combine($headers, $rowData);
                            $combined['__row_number__'] = $rowNumber;
                            $preview['provincial'][] = $combined;
                        }
                    }
                }
            }
            
            // Load Em_Contact_Info
            if ($sheet = $excel->getSheetByName('Em_Contact_Info')) {
                $data = $sheet->toArray();
                $headers = $data[6];
                for ($i = 7; $i < count($data); $i++) {
                    $rowNumber = $i + 1;
                    // Only show rows that have Employee ID and have data
                    if (in_array($rowNumber, $validRowNumbers) && $hasData($data[$i])) {
                        $rowData = $data[$i];
                        if (count($headers) === count($rowData)) {
                            $combined = array_combine($headers, $rowData);
                            $combined['__row_number__'] = $rowNumber;
                            $preview['emergency'][] = $combined;
                        }
                    }
                }
            }
            
            // Load Acc_Details
            if ($sheet = $excel->getSheetByName('Acc_Details')) {
                $data = $sheet->toArray();
                $headers = $data[6]; 
                for ($i = 7; $i < count($data); $i++) { 
                    $rowNumber = $i + 1;
                    // Only show rows that have Employee ID and have data
                    if (in_array($rowNumber, $validRowNumbers) && $hasData($data[$i])) {
                        $rowData = $data[$i];
                        if (count($headers) === count($rowData)) {
                            $combined = array_combine($headers, $rowData);
                            $combined['__row_number__'] = $rowNumber;
                            $preview['accounting'][] = $combined;
                        }
                    }
                }
            }
            
            // Load Hiring_Info
            if ($sheet = $excel->getSheetByName('Hiring_Info')) {
                $data = $sheet->toArray();
                $headers = $data[6];
                for ($i = 7; $i < count($data); $i++) {
                    $rowNumber = $i + 1;
                    // Only show rows that have Employee ID and have data
                    if (in_array($rowNumber, $validRowNumbers) && $hasData($data[$i])) {
                        $rowData = $data[$i];
                        if (count($headers) === count($rowData)) {
                            $combined = array_combine($headers, $rowData);
                            $combined['__row_number__'] = $rowNumber;
                            $preview['hiring'][] = $combined;
                        }
                    }
                }
            }
            
            return view('admin.records.users.add-multi', [
                'origin' => $origin,
                'excel_data' => $preview,
                'file_path' => $filePath
            ]);
            
        } catch (\Exception $e) {
            if (isset($filePath)) {
                \Storage::delete($filePath);
            }
            return back()->with('error', 'Error reading Excel file: ' . $e->getMessage())->withInput();
        }
    }

    public function save_multiple_users(Request $request)
    {
        $request->validate([
            'file_path' => 'required'
        ]);

        try {
            $fullPath = storage_path('app/' . $request->file_path);
            
            if (!file_exists($fullPath)) {
                return back()->with('error', 'File not found. Please upload the file again.');
            }
            
            // Actual importing of data
            $import = new UserRecordsImport(false);
            Excel::import($import, $fullPath);
            
            // Check for any failures during import
            $failures = $import->failures();
            
            if (!empty($failures)) {
                $errors = [];
                foreach ($failures as $failure) {
                    $row = $failure->row();
                    $errorsForRow = $failure->errors();
                    
                    foreach ($errorsForRow as $error) {
                        $errors[] = "Row {$row}: {$error}";
                    }
                }
                
                \Storage::delete($request->file_path);
                return back()->withErrors($errors);
            }
            
            \Storage::delete($request->file_path);

            return redirect()->route('admin.users')->with('success', 'Users added successfully.');
                
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $row = $failure->row();
                $errorsForRow = $failure->errors();
                
                foreach ($errorsForRow as $error) {
                    $errors[] = "Row {$row}: {$error}";
                }
            }
            
            \Storage::delete($request->file_path);
            return back()->withErrors($errors);
            
        } catch (\Exception $e) {
            if (isset($request->file_path)) {
                \Storage::delete($request->file_path);
            }
            
            return back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    public function approveItem($id)
    {
        $item = certifications::find($id);
        if ($item) {
            $type = 'Certification';
        } else {
            $item = Trainings::find($id);
            if ($item) {
                $type = 'Training';
            } else {
                $item = Licenses::find($id);
                if ($item) {
                    $type = 'License';
                } else {
                    $item = Education::find($id);
                    if ($item) {
                        $type = 'Education';
                    } else {
                        $item = Employment::find($id);
                        if ($item) {
                            $type = 'Employment';
                        } else {
                            $item = Research::find($id);
                            if ($item) {
                                $type = 'Research';
                            } else {
                                $item = Publication::find($id);
                                if ($item) {
                                    $type = 'Publication';
                                } else {
                                    $item = orgs::find($id);
                                    if ($item) {
                                        $type = 'Organization';
                                    } else {
                                        $item = dependencies::find($id);
                                        if ($item) {
                                            $type = 'Dependent';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$item) {
            return redirect()->back()->withErrors(['error' => 'Record not found.']);
        }

        $originalStatus = $item->status;

        $emp_id = $item->emp_id;

        // Update the item's status to Approved
        $item->update(['status' => 'Approved']);

        // If the original status was Pending, delete the associated request record
        if ($originalStatus === 'Pending') {
            \App\Models\requests::destroy($id);
        }

        return redirect()->route('admin.users.view',$emp_id)->with('msg', 'Record approved successfully.');
    }

    public function toReviewItem($id)
    {
        $item = certifications::find($id);
        if ($item) {
            $type = 'Certification';
        } else {
            $item = Trainings::find($id);
            if ($item) {
                $type = 'Training';
            } else {
                $item = Licenses::find($id);
                if ($item) {
                    $type = 'License';
                } else {
                    $item = Education::find($id);
                    if ($item) {
                        $type = 'Education';
                    } else {
                        $item = Employment::find($id);
                        if ($item) {
                            $type = 'Employment';
                        } else {
                            $item = Research::find($id);
                            if ($item) {
                                $type = 'Research';
                            } else {
                                $item = Publication::find($id);
                                if ($item) {
                                    $type = 'Publication';
                                } else {
                                    $item = orgs::find($id);
                                    if ($item) {
                                        $type = 'Organization';
                                    } else {
                                        $item = dependencies::find($id);
                                        if ($item) {
                                            $type = 'Dependent';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$item) {
            return redirect()->back()->withErrors(['error' => 'Record not found.']);
        }

        $originalStatus = $item->status;

        $emp_id = $item->emp_id;

        // Update the item's status to To-Review
        $item->update(['status' => 'To-review']);

        // If the original status was Pending, delete the associated request record
        if ($originalStatus === 'Pending') {
            \App\Models\requests::destroy($id);
        }

        return redirect()->route('admin.users.view',$emp_id)->with('msg', 'Record was sent to review.');
    }
}
