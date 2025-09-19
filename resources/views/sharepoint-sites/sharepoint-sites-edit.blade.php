<x-app-layout>
    <!-- This is where the link can be edited -->
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow px-8 py-6">

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('sharepoint-sites.edit-list') }}" 
                   class="inline-flex items-center bg-red-900 hover:bg-red-700 text-white px-4 py-2 rounded transition">
                    <img src="{{ asset('images/icons/back.png') }}" class="w-4 h-4 mr-2" alt="Back Icon">
                    Return to Link List
                </a>
            </div>

            <!-- Success & Error Messages -->
            @if(session('msg'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <strong>Success!</strong> {{ session('msg') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            @endif

            <h1 class="text-2xl font-bold mb-4">Edit Link</h1>

            <!-- Update Form -->
            <form method="POST" action="{{ route('sharepoint-sites.update', ['id' => $link->id]) }}">
                @csrf
                @method('PUT')

                <!-- Label -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Link Label</label>
                    <input type="text" name="label" value="{{ $link->label }}" required
                        class="mt-1 block w-full border border-gray-300 rounded p-2">
                </div>

                <!-- URL -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Link URL</label>
                    <input type="url" name="url" value="{{ $link->url }}" required
                        class="mt-1 block w-full border border-gray-300 rounded p-2">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description (hover tooltip)</label>
                    <textarea name="description" rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded p-2">{{ $link->description }}</textarea>
                </div>

                <!-- Category / Department / Office -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category-select-edit" class="rounded-lg w-full">
                            <option value="">Select Category</option>
                            @if(isset($categories) && is_array($categories))
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $link->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            @else
                                <option value="ISO" {{ $link->category == 'ISO' ? 'selected' : '' }}>ISO</option>
                                <option value="Planning and Review" {{ $link->category == 'Planning and Review' ? 'selected' : '' }}>Planning and Review</option>
                                <option value="Quality Assurance" {{ $link->category == 'Quality Assurance' ? 'selected' : '' }}>Quality Assurance</option>
                            @endif
                        </select>
                        <input id="category-input-edit" type="text" name="category" value="{{ $link->category }}" class="mt-2 block w-full border border-gray-300 rounded p-2" placeholder="Enter Category">
                        <small class="text-gray-400">Select an existing category or type a new one.</small>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <select id="department-select-edit" class="rounded-lg w-full">
                            <option value="">Select Department</option>
                        </select>
                        <input id="department-input-edit" class="rounded-lg w-full mt-2" name="department" type="text" placeholder="Enter Department" value="{{ $link->department }}"/>
                        <small class="text-gray-400">Select an existing department or type a new one.</small>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700">Office</label>
                        <select id="office-select-edit" class="rounded-lg w-full">
                            <option value="">Select Office</option>
                        </select>
                        <input id="office-input-edit" class="rounded-lg w-full mt-2" name="office" type="text" placeholder="Enter Office" value="{{ $link->office }}"/>
                        <small class="text-gray-400">Select an existing office or type a new one.</small>
                    </div>
                </div>

                <!-- Save Button -->
                <button type="submit"
                    class="flex items-center bg-red-900 hover:bg-red-700 text-white px-5 py-2 rounded transition">
                    <img src="{{ asset('images/icons/save.png') }}" class="w-5 h-5 mr-2" alt="Save Icon">
                    Save Changes
                </button>
            </form>

            <!-- Delete Form -->
            <form action="{{ route('sharepoint-sites.delete', $link->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this link?');" class="mt-4">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="flex items-center bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded transition">
                    <img src="{{ asset('images/icons/delete.png') }}" class="w-5 h-5 mr-2" alt="Delete Icon">
                    Delete Link
                </button>
            </form>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get departments by category and offices by department from PHP
            const departmentsByCategory = @json($departmentsByCategory ?? []);
            const officesByDepartment = @json($officesByDepartment ?? []);

            // Category dropdown/input sync
            const categorySelect = document.getElementById('category-select-edit');
            const categoryInput = document.getElementById('category-input-edit');
            const departmentSelect = document.getElementById('department-select-edit');
            const departmentInput = document.getElementById('department-input-edit');
            const officeSelect = document.getElementById('office-select-edit');
            const officeInput = document.getElementById('office-input-edit');

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

            // Initial population based on current link values
            if (categoryInput.value) {
                updateDepartmentDropdown(categoryInput.value);
                if (departmentInput.value) {
                    updateOfficeDropdown(departmentInput.value);
                }
            } else {
                departmentSelect.disabled = true;
                departmentInput.disabled = true;
                officeSelect.disabled = true;
                officeInput.disabled = true;
            }

            // Category dropdown/input sync
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
</x-app-layout>
