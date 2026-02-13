<x-app-layout>
    <div class="min-h-screen">
        <div class="w-full flex justify-center py-8">
            <div class="w-[95%] flex flex-col px-6 py-6 p-8 bg-white rounded-lg">

                {{-- Header Section --}}  
                <div class="w-full flex flex-col gap-4">
                    <a href="{{ $origin === 'all' ? route('admin.users') : route('admin.records') }}" 
                    class="w-[25%] rounded-lg flex items-center justify-center py-2 bg-red-900 text-white font-bold gap-1 hover:bg-red-700">
                        <img src="{{ asset('images/icons/back.png') }}" class="w-[20px] h-[20px]" alt="">
                        <h1>Back</h1>
                    </a>

                    <h1 class="text-[1.7rem] font-bold text-gray-700">Add Multiple Users</h1>
                </div>
            
                {{-- Upload Section --}} 
                <span class="text-xl text-gray-500 mt-4">Upload Users File</span>
                <span class="text-[0.8rem] text-gray-400">Ensure the uploaded file follows the official template for CSV updates. This is crucial for accurate data processing.</span>

                {{-- Error Messages --}}
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 my-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Validation Errors - Please fix these issues:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 my-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(!isset($excel_data))
                    <div class="w-full flex flex-col items-start gap-4 my-4">
                        <form action="{{ route('admin.users.addMultiple.load') }}" method="POST" class="flex flex-col items-start gap-4" enctype="multipart/form-data"> 
                            @csrf

                            {{-- Hidden Origin Input --}} 
                            <input type="hidden" name="origin" value="{{ $origin ?? 'all' }}">

                            {{-- File Input --}} 
                            <div class="flex items-center gap-2">
                                <input class="my-2" type="file" name="file" accept=".xlsx" required/>
                            </div>

                            {{-- Action Buttons --}} 
                            <div class="flex gap-4">
                                <div class="flex bg-red-900 hover:bg-red-700 text-white px-8 py-2 rounded-lg">
                                    <img src="{{ asset('images/icons/upload.png') }}" class="w-[20px] h-[20px] mr-2" alt="">
                                    <button type="submit">Upload File</button>
                                </div>
                                <a href="{{ asset('documents/user_templates/User_Data_Template.xlsx') }}" 
                                class="flex justify-center items-center bg-red-900 hover:bg-red-700 text-white px-12 py-2 rounded-lg gap-4">
                                    <img src="{{ asset('images/icons/download.svg') }}" class="w-[20px] h-[20px]" alt="">
                                    <span>Download Template</span>
                                </a>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Preview Section --}}
                    <div class="w-full mt-6">
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                            <p class="text-green-700 font-semibold">✓ File validated successfully!</p>
                            <p class="text-green-600 text-sm">Review the data below and click "Save to Database" to proceed.</p>
                        </div>

                        {{-- Personal Info Preview --}}
                        @if(isset($excel_data['personal']) && count($excel_data['personal']) > 0)
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Personal Information ({{ count($excel_data['personal']) }} records)</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border text-left text-sm font-semibold">Row #</th>
                                        <th class="px-4 py-2 border text-left text-sm">Employee ID</th>
                                        <th class="px-4 py-2 border text-left text-sm">First Name</th>
                                        <th class="px-4 py-2 border text-left text-sm">Last Name</th>
                                        <th class="px-4 py-2 border text-left text-sm">Department</th>
                                        <th class="px-4 py-2 border text-left text-sm">Gender</th>
                                        <th class="px-4 py-2 border text-left text-sm">Maiden Name</th>
                                        <th class="px-4 py-2 border text-left text-sm">Date of Birth</th>
                                        <th class="px-4 py-2 border text-left text-sm">Place of Birth</th>
                                        <th class="px-4 py-2 border text-left text-sm">Civil Status</th>
                                        <th class="px-4 py-2 border text-left text-sm">Religion</th>
                                        <th class="px-4 py-2 border text-left text-sm">Blood Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excel_data['personal'] as $row)
                                    <tr>
                                        <td class="px-4 py-2 border text-sm font-semibold bg-gray-50">{{ $row['__row_number__'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Employee ID'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['First Name'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Last Name'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Department'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Gender'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Maiden Name'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Date of Birth'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Place of Birth'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Civil Status'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Religion'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Blood Type'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Login Info Preview --}}
                        @if(isset($excel_data['login']) && count($excel_data['login']) > 0)
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Login Information ({{ count($excel_data['login']) }} records)</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border text-left text-sm font-semibold">Row #</th>
                                        <th class="px-4 py-2 border text-left text-sm">Email</th>
                                        <th class="px-4 py-2 border text-left text-sm">Role</th>
                                        <th class="px-4 py-2 border text-left text-sm">Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excel_data['login'] as $row)
                                    <tr>
                                        <td class="px-4 py-2 border text-sm font-semibold bg-gray-50">{{ $row['__row_number__'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Email'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Role'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ str_repeat('*', min(strlen($row['Password'] ?? ''), 10)) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Contact Info Preview --}}
                        @if(isset($excel_data['contact']) && count($excel_data['contact']) > 0)
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Contact Information ({{ count($excel_data['contact']) }} records)</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border text-left text-sm font-semibold">Row #</th>
                                        <th class="px-4 py-2 border text-left text-sm">House Number</th>
                                        <th class="px-4 py-2 border text-left text-sm">Street</th>
                                        <th class="px-4 py-2 border text-left text-sm">Barangay</th>
                                        <th class="px-4 py-2 border text-left text-sm">City</th>
                                        <th class="px-4 py-2 border text-left text-sm">Province</th>
                                        <th class="px-4 py-2 border text-left text-sm">Postal Code</th>
                                        <th class="px-4 py-2 border text-left text-sm">Home Phone</th>
                                        <th class="px-4 py-2 border text-left text-sm">Mobile Phone</th>
                                        <th class="px-4 py-2 border text-left text-sm">Email Address 1</th>
                                        <th class="px-4 py-2 border text-left text-sm">Email Address 2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excel_data['contact'] as $row)
                                    <tr>
                                        <td class="px-4 py-2 border text-sm font-semibold bg-gray-50">{{ $row['__row_number__'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['House Number'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Street'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Barangay'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['City'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Province'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Postal Code'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Home Phone'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Mobile Phone'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Email Address 1'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Email Address 2'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Provincial Contact Info Preview --}}
                        @if(isset($excel_data['provincial']) && count($excel_data['provincial']) > 0)
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Provincial Contact Information ({{ count($excel_data['provincial']) }} records)</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border text-left text-sm font-semibold">Row #</th>
                                        <th class="px-4 py-2 border text-left text-sm">House Number</th>
                                        <th class="px-4 py-2 border text-left text-sm">Street</th>
                                        <th class="px-4 py-2 border text-left text-sm">Barangay</th>
                                        <th class="px-4 py-2 border text-left text-sm">City</th>
                                        <th class="px-4 py-2 border text-left text-sm">Province</th>
                                        <th class="px-4 py-2 border text-left text-sm">Postal Code</th>
                                        <th class="px-4 py-2 border text-left text-sm">Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excel_data['provincial'] as $row)
                                    <tr>
                                        <td class="px-4 py-2 border text-sm font-semibold bg-gray-50">{{ $row['__row_number__'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['House Number'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Street'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Barangay'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['City'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Province'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Postal Code'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Phone'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Emergency Contact Info Preview --}}
                        @if(isset($excel_data['emergency']) && count($excel_data['emergency']) > 0)
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Emergency Contact Information ({{ count($excel_data['emergency']) }} records)</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border text-left text-sm font-semibold">Row #</th>
                                        <th class="px-4 py-2 border text-left text-sm">First Name</th>
                                        <th class="px-4 py-2 border text-left text-sm">Middle Name</th>
                                        <th class="px-4 py-2 border text-left text-sm">Last Name</th>
                                        <th class="px-4 py-2 border text-left text-sm">Relationship</th>
                                        <th class="px-4 py-2 border text-left text-sm">House Number</th>
                                        <th class="px-4 py-2 border text-left text-sm">Street</th>
                                        <th class="px-4 py-2 border text-left text-sm">City</th>
                                        <th class="px-4 py-2 border text-left text-sm">Province</th>
                                        <th class="px-4 py-2 border text-left text-sm">Postal Code</th>
                                        <th class="px-4 py-2 border text-left text-sm">Home Phone</th>
                                        <th class="px-4 py-2 border text-left text-sm">Mobile Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excel_data['emergency'] as $row)
                                    <tr>
                                        <td class="px-4 py-2 border text-sm font-semibold bg-gray-50">{{ $row['__row_number__'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['First Name'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Middle Name'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Last Name'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Relationship'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['House Number'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Street'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['City'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Province'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Postal Code'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Home Phone'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Mobile Phone'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Accounting Details Preview --}}
                        @if(isset($excel_data['accounting']) && count($excel_data['accounting']) > 0)
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Accounting Details ({{ count($excel_data['accounting']) }} records)</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border text-left text-sm font-semibold">Row #</th>
                                        <th class="px-4 py-2 border text-left text-sm">SSS Number</th>
                                        <th class="px-4 py-2 border text-left text-sm">Tax Identification Number</th>
                                        <th class="px-4 py-2 border text-left text-sm">Pag IBIG Number</th>
                                        <th class="px-4 py-2 border text-left text-sm">PhilHealth Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excel_data['accounting'] as $row)
                                    <tr>
                                        <td class="px-4 py-2 border text-sm font-semibold bg-gray-50">{{ $row['__row_number__'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['SSS Number'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Tax Identification Number'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Pag IBIG Number'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['PhilHealth Number'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Hiring Info Preview --}}
                        @if(isset($excel_data['hiring']) && count($excel_data['hiring']) > 0)
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Hiring Information ({{ count($excel_data['hiring']) }} records)</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border text-left text-sm font-semibold">Row #</th>
                                        <th class="px-4 py-2 border text-left text-sm">Date Hired</th>
                                        <th class="px-4 py-2 border text-left text-sm">Position</th>
                                        <th class="px-4 py-2 border text-left text-sm">Nature</th>
                                        <th class="px-4 py-2 border text-left text-sm">Tenure</th>
                                        <th class="px-4 py-2 border text-left text-sm">Non Tenure</th>
                                        <th class="px-4 py-2 border text-left text-sm">Required License</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excel_data['hiring'] as $row)
                                    <tr>
                                        <td class="px-4 py-2 border text-sm font-semibold bg-gray-50">{{ $row['__row_number__'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Date Hired'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Position'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Nature'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Tenure'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Non Tenure'] ?? '' }}</td>
                                        <td class="px-4 py-2 border text-sm">{{ $row['Required License'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex gap-4 mt-6">
                            <form action="{{ route('admin.users.addMultiple.save') }}" method="POST">
                                @csrf
                                <input type="hidden" name="origin" value="{{ $origin }}">
                                <input type="hidden" name="file_path" value="{{ $file_path }}">
                                <button type="submit" class="flex items-center bg-green-700 hover:bg-green-600 text-white px-8 py-2 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    </svg>
                                    Save to Database
                                </button>
                            </form>
                            <a href="{{ route('admin.users.addMultiple', ['origin' => $origin]) }}" 
                               class="flex items-center bg-gray-600 hover:bg-gray-500 text-white px-8 py-2 rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </div>
                @endif                                                                
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    a, button { 
        transition: 300ms;
    }
</style>