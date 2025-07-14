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
                        <select name="category" required class="mt-1 block w-full border border-gray-300 rounded p-2">
                            <option value="">Select Category</option>
                            <option value="ISO" {{ $link->category == 'ISO' ? 'selected' : '' }}>ISO</option>
                            <option value="Planning and Review" {{ $link->category == 'Planning and Review' ? 'selected' : '' }}>Planning and Review</option>
                            <option value="Quality Assurance" {{ $link->category == 'Quality Assurance' ? 'selected' : '' }}>Quality Assurance</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <input type="text" name="department" value="{{ $link->department }}" required
                            class="mt-1 block w-full border border-gray-300 rounded p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Office</label>
                        <input type="text" name="office" value="{{ $link->office }}"
                            class="mt-1 block w-full border border-gray-300 rounded p-2">
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
</x-app-layout>
