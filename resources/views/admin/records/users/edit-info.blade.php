<!-- resources/views/admin/records/users/edit-info.blade.php -->
<x-app-layout>
    <div class="flex justify-center py-8">
        <div class="w-[95%] max-w-4xl bg-white rounded-lg p-8 shadow-lg">
            <h1 class="text-3xl font-extrabold text-gray-800 text-center mb-4">Edit User Profile</h1>

            <!-- Breadcrumb -->
            <div class="flex items-center justify-center gap-2 mt-4">
                <img src="{{ asset('images/icons/users_maroon.png') }}" class="w-6 h-6" alt="Users Icon">
                <a href="{{ route('admin.users') }}" class="text-red-900 hover:text-red-700 font-semibold">Users</a>
                <span class="text-lg">&gt;</span>
                <span class="font-semibold">{{ $data->emp_id }}</span>
            </div>

            <hr class="opacity-90 my-4">

            <!-- If there's a need to display error messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif

            <!-- Decide which section fields to show -->
            <form method="POST" 
                  action="{{ route('admin.users.update', ['id' => $data->emp_id, 'section' => $section]) }}" 
                  class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 
                  PERSONAL DATA FIELDS
                  Only show if $section == 'personal'
                -->
                @if($section === 'personal')
                    <h2 class="text-xl font-semibold text-gray-700">Edit Personal Data</h2>
                    <div>
                        <label for="emp_fname" class="block text-sm font-semibold text-gray-700">First Name</label>
                        <input type="text" id="emp_fname" name="emp_fname" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('emp_fname', $data->emp_fname) }}" required>
                    </div>
                    <div>
                        <label for="emp_mname" class="block text-sm font-semibold text-gray-700">Middle Name</label>
                        <input type="text" id="emp_mname" name="emp_mname" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('emp_mname', $data->emp_mname) }}">
                    </div>
                    <div>
                        <label for="emp_lname" class="block text-sm font-semibold text-gray-700">Last Name</label>
                        <input type="text" id="emp_lname" name="emp_lname" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('emp_lname', $data->emp_lname) }}" required>
                    </div>
                    <div>
                        <label for="emp_dept" class="block text-sm font-semibold text-gray-700">
                            Department
                        </label>

                        <select id="emp_dept" name="emp_dept"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900">

                            @foreach ($dept as $department)
                                <option value="{{ $department->code }}"
                                    {{ old('emp_dept', $data->emp_dept) == $department->code ? 'selected' : '' }}>
                                    {{ $department->dept }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    <div>
                        <label for="emp_gender" class="block text-sm font-semibold text-gray-700">Gender</label>
                        <select id="emp_gender" name="emp_gender" 
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900">
                            <option value="Male"   {{ $data->emp_gender == 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $data->emp_gender == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label for="emp_dob" class="block text-sm font-semibold text-gray-700">Date of Birth</label>
                        <input type="date" id="emp_dob" name="emp_dob" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('emp_dob', $data->emp_dob ? $data->emp_dob->format('Y-m-d') : '') }}">
                    </div>
                    <div>
                        <label for="emp_pob" class="block text-sm font-semibold text-gray-700">Place of Birth</label>
                        <input type="text" id="emp_pob" name="emp_pob" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('emp_pob', $data->emp_pob) }}">
                    </div>
                    <div>
                        <label for="emp_cStatus" class="block text-sm font-semibold text-gray-700">Civil Status</label>
                        <select id="emp_cStatus" name="emp_cStatus" 
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900">
                            <option value="Single"   {{ $data->emp_cStatus == 'Single'   ? 'selected' : '' }}>Single</option>
                            <option value="Married"   {{ $data->emp_cStatus == 'Married'   ? 'selected' : '' }}>Married</option>
                            <option value="Widowed"   {{ $data->emp_cStatus == 'Widowed'   ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated"   {{ $data->emp_cStatus == 'Separated'   ? 'selected' : '' }}>Separated</option>
                        </select>
                    </div>
                    <div>
                        <label for="emp_religion" class="block text-sm font-semibold text-gray-700">Religion</label>
                        <input type="text" id="emp_religion" name="emp_religion" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('emp_religion', $data->emp_religion) }}">
                    </div>
                    <div>
                        <label for="emp_blood_type" class="block text-sm font-semibold text-gray-700">Blood Type</label>
                        <input type="text" id="emp_blood_type" name="emp_blood_type" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('emp_blood_type', $data->emp_blood_type) }}">
                    </div>
                    <!-- Add other personal data fields as needed -->
                @endif

                <!-- 
                  CONTACT INFORMATION 
                  Only show if $section == 'contact'
                -->
                @if($section === 'contact')
                    <h2 class="text-xl font-semibold text-gray-700">Edit Contact Information</h2>
                    <div>
                        <label for="emp_houseno" class="block text-sm font-semibold text-gray-700">House No.</label>
                        <input type="text" id="emp_houseno" name="emp_houseno" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('emp_houseno', $data->emp_houseno) }}">
                    </div>
                    <div>
                        <label for="street" class="block text-sm font-semibold text-gray-700">Street</label>
                        <input type="text" id="street" name="street" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('street', $data->street) }}">
                    </div>
                    <div>
                        <label for="brgy" class="block text-sm font-semibold text-gray-700">Barangay</label>
                        <input type="text" id="brgy" name="brgy" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('brgy', $data->brgy) }}">
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-semibold text-gray-700">City</label>
                        <input type="text" id="city" name="city"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('city', $data->city) }}">
                    </div>
                    <div>
                        <label for="province" class="block text-sm font-semibold text-gray-700">Province</label>
                        <input type="text" id="province" name="province"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('province', $data->province) }}">
                    </div>
                    <div>
                        <label for="postal_code" class="block text-sm font-semibold text-gray-700">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('postal_code', $data->postal_code) }}">
                    </div>
                    <div>
                        <label for="home_phone" class="block text-sm font-semibold text-gray-700">Home Phone No.</label>
                        <input type="text" id="home_phone" name="home_phone" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('home_phone', $data->home_phone) }}">
                    </div>
                    <div>
                        <label for="mobile_phone" class="block text-sm font-semibold text-gray-700">Mobile Phone No.</label>
                        <input type="text" id="mobile_phone" name="mobile_phone" 
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('mobile_phone', $data->mobile_phone) }}">
                    </div>
                    <div>
                        <label for="email_address_1" class="block text-sm font-semibold text-gray-700">Primary Email</label>
                        <input type="email" id="email_address_1" name="email_address_1"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('email_address_1', $data->email_address_1) }}">
                    </div>
                    <div>
                        <label for="email_address_2" class="block text-sm font-semibold text-gray-700">Secondary Email</label>
                        <input type="email" id="email_address_2" name="email_address_2"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('email_address_2', $data->email_address_2) }}">
                    </div>
                @endif

                <!-- 
                  PROVINCIAL CONTACT
                -->
                @if($section === 'provincial')
                    <h2 class="text-xl font-semibold text-gray-700">Edit Provincial Contact</h2>
                    <div>
                        <label for="pc_emp_houseno" class="block text-sm font-semibold text-gray-700">House No.</label>
                        <input type="text" id="pc_emp_houseno" name="pc_emp_houseno"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('pc_emp_houseno', optional($data->provincial_contact)->pc_emp_houseno) }}" required>
                    </div>
                    <div>
                        <label for="pc_street" class="block text-sm font-semibold text-gray-700">Street</label>
                        <input type="text" id="pc_street" name="pc_street"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('pc_street', optional($data->provincial_contact)->pc_street) }}">
                    </div>
                    <div>
                        <label for="pc_brgy" class="block text-sm font-semibold text-gray-700">Barangay</label>
                        <input type="text" id="pc_brgy" name="pc_brgy"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('pc_brgy', optional($data->provincial_contact)->pc_brgy) }}">
                    </div>
                    <div>
                        <label for="pc_city" class="block text-sm font-semibold text-gray-700">City</label>
                        <input type="text" id="pc_city" name="pc_city"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('pc_city', optional($data->provincial_contact)->pc_city) }}">
                    </div>
                    <div>
                        <label for="pc_province" class="block text-sm font-semibold text-gray-700">Province</label>
                        <input type="text" id="pc_province" name="pc_province"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('pc_province', optional($data->provincial_contact)->pc_province) }}">
                    </div>
                    <div>
                        <label for="pc_postal_code" class="block text-sm font-semibold text-gray-700">Postal Code</label>
                        <input type="text" id="pc_postal_code" name="pc_postal_code"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('pc_postal_code', optional($data->provincial_contact)->pc_postal_code) }}">
                    </div>
                    <div>
                        <label for="pc_phone" class="block text-sm font-semibold text-gray-700">Provincial Phone Number</label>
                        <input type="text" id="pc_phone" name="pc_phone"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('pc_phone', optional($data->provincial_contact)->pc_phone) }}">
                    </div>
                @endif

                <!-- 
                  EMERGENCY CONTACT
                -->
                @if($section === 'emergency')
                    <h2 class="text-xl font-semibold text-gray-700">Edit Emergency Contact</h2>
                    <div>
                        <label for="cp_fname" class="block text-sm font-semibold text-gray-700">First Name</label>
                        <input type="text" id="cp_fname" name="cp_fname"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_fname', optional($data->emergency_contact)->cp_fname) }}" required>
                    </div>
                    <div>
                        <label for="cp_mname" class="block text-sm font-semibold text-gray-700">Middle Name</label>
                        <input type="text" id="cp_mname" name="cp_mname"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_mname', optional($data->emergency_contact)->cp_mname) }}">
                    </div>
                    <div>
                        <label for="cp_lname" class="block text-sm font-semibold text-gray-700">Last Name</label>
                        <input type="text" id="cp_lname" name="cp_lname"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_lname', optional($data->emergency_contact)->cp_lname) }}" required>
                    </div>
                    <div>
                        <label for="cp_relationship" class="block text-sm font-semibold text-gray-700">Relationship</label>
                        <input type="text" id="cp_relationship" name="cp_relationship"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_relationship', optional($data->emergency_contact)->cp_relationship) }}">
                    </div>
                    <div>
                        <label for="cp_house_no" class="block text-sm font-semibold text-gray-700">House No.</label>
                        <input type="text" id="cp_house_no" name="cp_house_no"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_house_no', optional($data->emergency_contact)->cp_house_no) }}">
                    </div>
                    <div>
                        <label for="cp_street" class="block text-sm font-semibold text-gray-700">Street</label>
                        <input type="text" id="cp_street" name="cp_street"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_street', optional($data->emergency_contact)->cp_street) }}">
                    </div>
                    <div>
                        <label for="cp_brgy" class="block text-sm font-semibold text-gray-700">Barangay</label>
                        <input type="text" id="cp_brgy" name="cp_brgy"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_brgyt', optional($data->emergency_contact)->cp_brgy) }}">
                    </div>
                    <div>
                        <label for="cp_city" class="block text-sm font-semibold text-gray-700">City</label>
                        <input type="text" id="cp_city" name="cp_city"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_city', optional($data->emergency_contact)->cp_city) }}">
                    </div>
                    <div>
                        <label for="cp_province" class="block text-sm font-semibold text-gray-700">Province</label>
                        <input type="text" id="cp_province" name="cp_province"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_province', optional($data->emergency_contact)->cp_province) }}">
                    </div>
                    <div>
                        <label for="cp_postal_code" class="block text-sm font-semibold text-gray-700">Postal Code</label>
                        <input type="text" id="cp_postal_code" name="cp_postal_code"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_postal_code', optional($data->emergency_contact)->cp_postal_code) }}">
                    </div>
                    <div>
                        <label for="cp_home_phone" class="block text-sm font-semibold text-gray-700">Home Phone</label>
                        <input type="text" id="cp_home_phone" name="cp_home_phone"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_home_phone', optional($data->emergency_contact)->cp_home_phone) }}">
                    </div>
                    <div>
                        <label for="cp_mobile_no" class="block text-sm font-semibold text-gray-700">Mobile No.</label>
                        <input type="text" id="cp_mobile_no" name="cp_mobile_no"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('cp_mobile_no', optional($data->emergency_contact)->cp_mobile_no) }}">
                    </div>
                @endif

                <!-- 
                  ACCOUNTING DATA
                -->
                @if($section === 'accounting')
                    <h2 class="text-xl font-semibold text-gray-700">Edit Accounting Data</h2>
                    <div>
                        <label for="sss_no" class="block text-sm font-semibold text-gray-700">SSS No.</label>
                        <input type="text" id="sss_no" name="sss_no"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('sss_no', optional($data->accounting_details)->sss_no) }}">
                    </div>
                    <div>
                        <label for="tax_no" class="block text-sm font-semibold text-gray-700">Tax ID No.</label>
                        <input type="text" id="tax_no" name="tax_no"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('tax_no', optional($data->accounting_details)->tax_no) }}">
                    </div>
                    <div>
                        <label for="pagibig_no" class="block text-sm font-semibold text-gray-700">Pag-Ibig No.</label>
                        <input type="text" id="pagibig_no" name="pagibig_no"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('pagibig_no', optional($data->accounting_details)->pagibig_no) }}">
                    </div>
                    <div>
                        <label for="philhealth_no" class="block text-sm font-semibold text-gray-700">PhilHealth No.</label>
                        <input type="text" id="philhealth_no" name="philhealth_no"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                               value="{{ old('philhealth_no', optional($data->accounting_details)->philhealth_no) }}">
                    </div>
                @endif
                @if($section === 'login')
                <h2 class="text-xl font-semibold text-gray-700">Edit Login Information</h2>
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" id="email" name="email"
                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                        value="{{ old('email', $data->login->email) }}" autocomplete="off" required>
                </div>
                <div class="mt-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700">New Password</label>
                    <input type="password" id="password" name="password"
                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                        placeholder="Leave blank if not changing" autocomplete="new-password">
                </div>
                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900"
                        placeholder="Leave blank if not changing" autocomplete="new-password">
                </div>
                <div class="mt-4">
                    <label for="role" class="block text-sm font-semibold text-gray-700">Role</label>
                    <select id="role" name="role"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-red-900 focus:border-red-900">
                        <option value="Employee" {{ (old('role', $data->login->role) == 'Employee') ? 'selected' : '' }}>Employee</option>
                        <option value="SuperAdmin" {{ (old('role', $data->login->role) == 'SuperAdmin') ? 'selected' : '' }}>SuperAdmin</option>
                        <option value="HR Admin" {{ (old('role', $data->login->role) == 'HR Admin') ? 'selected' : '' }}>HR Admin</option>
                        <option value="Dean" {{ (old('role', $data->login->role) == 'Dean') ? 'selected' : '' }}>Dean</option>
                        <option value="IDC Admin" {{ (old('role', $data->login->role) == 'IDC Admin') ? 'selected' : '' }}>IDC Admin</option>
                        <option value="ISO Document Handler" {{ (old('role', $data->login->role) == 'ISO Document Handler') ? 'selected' : '' }}>ISO Document Handler</option>
                    </select>
                </div>
            @endif

                <!-- If no valid section is chosen or you want a default. 
                     This is optional. You could omit it entirely. -->
                @if(!$section)
                    <p class="text-gray-600 italic">Please select a section from the profile page.</p>
                @endif

                <!-- Submit Button (only if there's a valid $section) -->
                @if($section)
                <div class="flex justify-between pt-4">
                    <a href="{{ route('admin.users.view', $data->emp_id) }}" 
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">
                        Return to Previous Page
                    </a>
                    <button type="submit" class="px-6 py-2 text-white bg-red-900 rounded-md hover:bg-red-700">
                        Save Changes
                    </button>
                </div>
                @endif

            </form>
        </div>
    </div>
</x-app-layout>
