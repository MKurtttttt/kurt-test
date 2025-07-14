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
                                <select class="rounded-lg w-full" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="ISO">ISO</option>
                                    <option value="Planning and Review">Planning and Review</option>
                                    <option value="Quality Assurance">Quality Assurance</option>
                                </select>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">DEPARTMENT <span class="font-bold text-red-500">*</span></h1>
                                <input class="rounded-lg w-full" name="department" type="text" required/>
                            </div>
                            <div class="flex flex-col">
                                <h1 class="text-gray-500">OFFICE</h1>
                                <input class="rounded-lg w-full" name="office" type="text"/>
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

<style>
    .maroon {
        background-color: maroon;
    }
</style>
