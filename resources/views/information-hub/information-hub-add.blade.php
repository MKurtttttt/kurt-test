<x-app-layout>
    <!-- This is the page where links can be added -->
    <div class="min-h-screen">
        <div class="flex justify-center items-center w-full py-8">
            <div class="flex-col w-[95%] bg-white rounded-lg py-8">
                <div class="px-8 pb-4">
                    <a href="{{ route('information-hub.dashboard') }}" class="inline-flex gap-1 items-center bg-red-900 hover:bg-red-700 px-6 py-1 text-white rounded-xl">
                        <img src="{{ asset('images/icons/back.png') }}" class="w-[20px] h-[20px]" alt="">
                        <span>Return to Information Hub</span>
                    </a>
                </div>
                <div class="w-full flex flex-col items-center justify-center px-8 py-4 leading-tight">
                    <img class="w-[120px] h-[120px] my-0" src="{{ asset('images/logo-circle.png') }}" />
                    <h1 class="text-[3rem] font-bold text-gray-700"> ADD INFORMATION HUB LINK </h1>
                    <span class="text-[0.7rem] text-gray-400">
                        Please fill out the details below to add a new Information Hub link. Make sure the information is accurate and categorized properly.
                    </span>
                </div>

                <div>
                    <form class="flex-col w-full px-8" action="{{ route('information-hub.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <!-- Row 1: Title and URL -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">TITLE <span class="font-bold text-red-500">*</span></h1>
                                <input class="rounded-lg w-full" name="title" type="text" required/>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">LINK URL <span class="font-bold text-red-500">*</span></h1>
                                <input class="rounded-lg w-full" name="url" type="url" required/>
                            </div>
                        </div>

                        <!-- Row 2: Category, Sub-Category, Type -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">CATEGORY <span class="font-bold text-red-500">*</span></h1>
                                <select id="category-select-add" class="rounded-lg w-full">
                                    <option value="">Select Category</option>
                                    @if(isset($categories) && is_array($categories))
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input id="category-input-add" class="rounded-lg w-full mt-2" name="category" type="text" placeholder="Enter Category"/>
                                <small class="text-gray-400">Select an existing category or type a new one.</small>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">SUB-CATEGORY</h1>
                                <select id="sub-category-select-add" class="rounded-lg w-full" disabled>
                                    <option value="">Select Sub-Category</option>
                                </select>
                                <input id="sub-category-input-add" class="rounded-lg w-full mt-2" name="sub_category" type="text" placeholder="Enter Sub-Category" disabled/>
                                <small class="text-gray-400">Select an existing sub-category or type a new one</small>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">TYPE</h1>
                                <select class="rounded-lg w-full" name="type">
                                    <option value="">Select Type</option>
                                    <option value="Document">Document</option>
                                    <option value="Video">Video</option>
                                    <option value="Other">Other</option>
                                </select>
                                <small class="text-gray-400">Choose the type of content</small>
                            </div>
                        </div>

                        <!-- Row 3: Image Upload -->
                        <div class="mt-4">
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">IMAGE</h1>
                                <input class="w-full" name="image" type="file" accept="image/*"/>
                                <small class="text-gray-400">Upload an image for this link.</small>
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
        // Get sub-categories by category from PHP
        const subCatByCategory = @json($subCatByCategory);

        // Category dropdown/input sync
        const categorySelect = document.getElementById('category-select-add');
        const categoryInput = document.getElementById('category-input-add');
        const subCategorySelect = document.getElementById('sub-category-select-add');
        const subCategoryInput = document.getElementById('sub-category-input-add');

        function updateSubCategoryDropdown(selectedCategory) {
            subCategorySelect.innerHTML = '<option value="">Select Sub-Category</option>';
            if (subCatByCategory[selectedCategory]) {
                subCatByCategory[selectedCategory].forEach(function(subCat) {
                    const opt = document.createElement('option');
                    opt.value = subCat;
                    opt.textContent = subCat;
                    subCategorySelect.appendChild(opt);
                });
            }
            subCategorySelect.disabled = false;
            subCategoryInput.disabled = false;
        }

        categorySelect.addEventListener('change', function() {
            if (this.value) {
                categoryInput.value = this.value;
                updateSubCategoryDropdown(this.value);
            } else {
                categoryInput.value = '';
                subCategorySelect.innerHTML = '<option value="">Select Sub-Category</option>';
                subCategorySelect.disabled = true;
                subCategoryInput.value = '';
                subCategoryInput.disabled = true;
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
                subCategorySelect.disabled = true;
                subCategoryInput.value = '';
                subCategoryInput.disabled = true;
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

<style>
    .maroon {
        background-color: maroon;
        transition: 300ms;
    }
    
    .maroon:hover {
        background-color: #A84655;
    }
</style>