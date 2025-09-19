<x-app-layout>
    <!-- This is the page where links can be added -->
    <div class="min-h-screen">
        <div class="flex justify-center items-center w-full py-8">
            <div class="flex-col w-[95%] bg-white rounded-lg py-8">
                <div class="px-8 pb-4">
                    <a href="{{ route('sharepoint-sites.dashboard') }}" class="inline-flex gap-1 items-center bg-red-900 hover:bg-red-700 px-6 py-1 text-white rounded-xl">
                        <img src="{{ asset('images/icons/back.png') }}" class="w-[20px] h-[20px]" alt="">
                        <span>Return to SharePoint Sites</span>
                    </a>
                </div>
                <div class="w-full flex flex-col items-center justify-center px-8 py-4 leading-tight">
                    <img class="w-[120px] h-[120px] my-0" src="{{ asset('images/logo-circle.png') }}" />
                    <h1 class="text-[3rem] font-bold text-gray-700"> ADD SHAREPOINT LINK </h1>
                    <span class="text-[0.7rem] text-gray-400">
                        Please fill out the details below to add a new SharePoint link. Make sure the information is accurate and categorized properly.
                    </span>
                </div>

                <div>
                    <form class="flex-col w-full px-8" action="{{ route('sharepoint-sites.store') }}" method="POST">
                        @csrf
                        @method('POST')

                        <!-- Row 1: Link Label and URL -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">LINK LABEL <span class="font-bold text-red-500">*</span></h1>
                                <input class="rounded-lg w-full" name="label" type="text" required/>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">LINK URL <span class="font-bold text-red-500">*</span></h1>
                                <input class="rounded-lg w-full" name="url" type="url" required/>
                            </div>
                        </div>

                        <!-- Row 2: Description -->
                        <div class="mt-4">
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">DESCRIPTION</h1>
                                <textarea class="rounded-lg w-full" name="description" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Row 3: Category, Department, Office -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">CATEGORY <span class="font-bold text-red-500">*</span></h1>
                                <select id="category-select-add" class="rounded-lg w-full">
                                    <option value="">Select Category</option>
                                    @if(isset($categories) && is_array($categories))
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                    @else
                                        <option value="ISO">ISO</option>
                                        <option value="Planning and Review">Planning and Review</option>
                                        <option value="Quality Assurance">Quality Assurance</option>
                                    @endif
                                </select>
                                <input id="category-input-add" class="rounded-lg w-full mt-2" name="category" type="text" placeholder="Enter Category"/>
                                <small class="text-gray-400">Select an existing category or type a new one.</small>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">DEPARTMENT <span class="font-bold text-red-500">*</span></h1>
                                <select id="department-select-add" class="rounded-lg w-full">
                                    <option value="">Select Department</option>
                                </select>
                                <input id="department-input-add" class="rounded-lg w-full mt-2" name="department" type="text" placeholder="Enter Department"/>
                                <small class="text-gray-400">Select an existing department or type a new one.</small>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">OFFICE</h1>
                                <select id="office-select-add" class="rounded-lg w-full">
                                    <option value="">Select Office</option>
                                </select>
                                <input id="office-input-add" class="rounded-lg w-full mt-2" name="office" type="text" placeholder="Enter Office"/>
                                <small class="text-gray-400">Select an existing office or type a new one.</small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="w-full flex justify-end py-4">
                            <button class="maroon text-white px-12 py-2 rounded-md" type="submit">ADD LINK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get departments by category and offices by department from PHP
            const departmentsByCategory = @json($departmentsByCategory);
            const officesByDepartment = @json($officesByDepartment);

            // Category dropdown/input sync
            const categorySelect = document.getElementById('category-select-add');
            const categoryInput = document.getElementById('category-input-add');
            const departmentSelect = document.getElementById('department-select-add');
            const departmentInput = document.getElementById('department-input-add');
            const officeSelect = document.getElementById('office-select-add');
            const officeInput = document.getElementById('office-input-add');

            function updateDepartmentDropdown(selectedCategory) {
                departmentSelect.innerHTML = '<option value="">Select Department</option>';
                if (departmentsByCategory[selectedCategory]) {
                    departmentsByCategory[selectedCategory].forEach(function(dept) {
                        const opt = document.createElement('option');
                        opt.value = dept;
                        opt.textContent = dept;
                        departmentSelect.appendChild(opt);
                    });
                }
                departmentSelect.disabled = false;
                departmentInput.disabled = false;
                // Reset office dropdown/input when category changes
                updateOfficeDropdown('');
            }

            function updateOfficeDropdown(selectedDepartment) {
                officeSelect.innerHTML = '<option value="">Select Office</option>';
                if (officesByDepartment[selectedDepartment]) {
                    officesByDepartment[selectedDepartment].forEach(function(office) {
                        const opt = document.createElement('option');
                        opt.value = office;
                        opt.textContent = office;
                        officeSelect.appendChild(opt);
                    });
                }
                officeSelect.disabled = false;
                officeInput.disabled = false;
            }

            categorySelect.addEventListener('change', function() {
                if (this.value) {
                    categoryInput.value = this.value;
                    updateDepartmentDropdown(this.value);
                } else {
                    categoryInput.value = '';
                    updateDepartmentDropdown('');
                }
            });
            categoryInput.addEventListener('input', function() {
                const inputValue = this.value;
                let found = false;
                for (let i = 0; i < categorySelect.options.length; i++) {
                    if (categorySelect.options[i].value === inputValue) {
                        categorySelect.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    categorySelect.selectedIndex = 0;
                }
                updateDepartmentDropdown(inputValue);
            });

            // Department dropdown/input sync
            if (departmentSelect && departmentInput) {
                departmentSelect.addEventListener('change', function() {
                    if (this.value) {
                        departmentInput.value = this.value;
                        updateOfficeDropdown(this.value);
                    } else {
                        departmentInput.value = '';
                        updateOfficeDropdown('');
                    }
                });
                departmentInput.addEventListener('input', function() {
                    const inputValue = this.value;
                    let found = false;
                    for (let i = 0; i < departmentSelect.options.length; i++) {
                        if (departmentSelect.options[i].value === inputValue) {
                            departmentSelect.selectedIndex = i;
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        departmentSelect.selectedIndex = 0;
                    }
                    updateOfficeDropdown(inputValue);
                });
            }

            // Office dropdown/input sync
            if (officeSelect && officeInput) {
                officeSelect.addEventListener('change', function() {
                    if (this.value) {
                        officeInput.value = this.value;
                    }
                });
                officeInput.addEventListener('input', function() {
                    const inputValue = this.value;
                    let found = false;
                    for (let i = 0; i < officeSelect.options.length; i++) {
                        if (officeSelect.options[i].value === inputValue) {
                            officeSelect.selectedIndex = i;
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        officeSelect.selectedIndex = 0;
                    }
                });
            }
        });
    </script>
<style>
    .maroon {
        background-color: maroon;
    }
</style>
