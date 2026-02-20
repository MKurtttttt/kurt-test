<x-app-layout>
    <!-- This is where the link can be edited -->
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow px-8 py-6">

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('information-hub.edit-list') }}" 
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
            <form method="POST" action="{{ route('information-hub.update', ['id' => $link->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $link->title }}" required
                        class="mt-1 block w-full border border-gray-300 rounded p-2">
                </div>

                <!-- URL -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Link URL <span class="text-red-500">*</span></label>
                    <input type="url" name="url" value="{{ $link->url }}" required
                        class="mt-1 block w-full border border-gray-300 rounded p-2">
                </div>

                <!-- Category / Sub-Category / Type -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category-select-edit" class="rounded-lg w-full border border-gray-300 p-2">
                            <option value="">Select Category</option>
                            @if(isset($categories) && is_array($categories))
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $link->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            @endif
                        </select>
                        <input id="category-input-edit" type="text" name="category" value="{{ $link->category }}" 
                            class="mt-2 block w-full border border-gray-300 rounded p-2" placeholder="Enter Category">
                        <small class="text-gray-400">Select an existing category or type a new one.</small>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700">Sub-Category</label>
                        <select id="sub-category-select-edit" class="rounded-lg w-full border border-gray-300 p-2">
                            <option value="">Select Sub-Category</option>
                        </select>
                        <input id="sub-category-input-edit" class="rounded-lg w-full mt-2 border border-gray-300 p-2" 
                            name="sub_category" type="text" placeholder="Enter Sub-Category" value="{{ $link->sub_category }}"/>
                        <small class="text-gray-400">Select an existing sub-category or type a new one (optional).</small>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select class="rounded-lg w-full border border-gray-300 p-2" name="type">
                            <option value="">Select Type</option>
                            <option value="Document" {{ $link->type == 'Document' ? 'selected' : '' }}>Document</option>
                            <option value="Video" {{ $link->type == 'Video' ? 'selected' : '' }}>Video</option>
                            <option value="Other" {{ $link->type == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <small class="text-gray-400">Choose the type of content (affects image display).</small>
                    </div>
                </div>

                <!-- Current Image Display -->
                @if($link->image_path)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset($link->image_path) }}" alt="Current image" class="w-32 h-32 object-cover border rounded">
                            <div>
                                <p class="text-sm text-gray-600">{{ basename($link->image_path) }}</p>
                                <label class="flex items-center mt-2">
                                    <input type="checkbox" name="remove_image" value="1" class="mr-2">
                                    <span class="text-sm text-red-600">Remove current image</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Image Upload -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">{{ $link->image_path ? 'Replace Image' : 'Upload Image' }}</label>
                    <input class="mt-1 block w-full border border-gray-300 rounded p-2" name="image" type="file" accept="image/*"/>
                    <small class="text-gray-400">Upload a new image (optional). Recommended size: 192x192px for square, 144x192px for documents, 192x144px for videos.</small>
                </div>

                <!-- Save Button -->
                <button type="submit"
                    class="flex items-center bg-red-900 hover:bg-red-700 text-white px-5 py-2 rounded transition">
                    <img src="{{ asset('images/icons/save.png') }}" class="w-5 h-5 mr-2" alt="Save Icon">
                    Save Changes
                </button>
            </form>

            <!-- Delete Form -->
            <form action="{{ route('information-hub.delete', $link->id) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this link? This will also delete the associated image.');" 
                  class="mt-4">
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
            // Get sub-categories by category from PHP
            const subCatByCategory = @json($subCatByCategory ?? []);

            // Category dropdown/input sync
            const categorySelect = document.getElementById('category-select-edit');
            const categoryInput = document.getElementById('category-input-edit');
            const subCategorySelect = document.getElementById('sub-category-select-edit');
            const subCategoryInput = document.getElementById('sub-category-input-edit');

            function updateSubCategoryDropdown(selectedCategory) {
                subCategorySelect.innerHTML = '<option value="">Select Sub-Category</option>';
                if (subCatByCategory[selectedCategory]) {
                    subCatByCategory[selectedCategory].forEach(function(subCat) {
                        const opt = document.createElement('option');
                        opt.value = subCat;
                        opt.textContent = subCat;
                        // Pre-select if it matches current link's sub_category
                        if (subCat === subCategoryInput.value) {
                            opt.selected = true;
                        }
                        subCategorySelect.appendChild(opt);
                    });
                }
            }

            // Initial population based on current link values
            if (categoryInput.value) {
                updateSubCategoryDropdown(categoryInput.value);
            }

            // Category dropdown/input sync
            categorySelect.addEventListener('change', function() {
                if (this.value) {
                    categoryInput.value = this.value;
                    updateSubCategoryDropdown(this.value);
                } else {
                    categoryInput.value = '';
                    subCategorySelect.innerHTML = '<option value="">Select Sub-Category</option>';
                    subCategoryInput.value = '';
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
                
                if (inputValue) {
                    updateSubCategoryDropdown(inputValue);
                } else {
                    subCategorySelect.innerHTML = '<option value="">Select Sub-Category</option>';
                }
            });

            // Sub-Category dropdown/input sync
            if (subCategorySelect && subCategoryInput) {
                subCategorySelect.addEventListener('change', function() {
                    if (this.value) {
                        subCategoryInput.value = this.value;
                    } else {
                        subCategoryInput.value = '';
                    }
                });

                subCategoryInput.addEventListener('input', function() {
                    const inputValue = this.value;
                    let found = false;
                    for (let i = 0; i < subCategorySelect.options.length; i++) {
                        if (subCategorySelect.options[i].value === inputValue) {
                            subCategorySelect.selectedIndex = i;
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        subCategorySelect.selectedIndex = 0;
                    }
                });
            }
        });
    </script>
</x-app-layout>