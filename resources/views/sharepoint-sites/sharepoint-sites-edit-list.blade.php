<x-app-layout>
    <!-- This is where the table of links to choose which one to edit -->
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Select a Link to Edit</h1>

        <!-- Back Button -->
        <a href="{{ route('sharepoint-sites.dashboard') }}" 
           class="inline-flex items-center bg-red-900 hover:bg-red-700 text-white px-4 py-2 rounded mb-4 transition">
            <img src="{{ asset('images/icons/back.png') }}" class="w-4 h-4 mr-2" alt="Back Icon">
            Return to Dashboard
        </a>

        <div class="overflow-y-auto max-h-[70vh]">
            <table class="table-auto w-full border">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-2">Label</th>
                        <th class="px-4 py-2">Category</th>
                        <th class="px-4 py-2">Department</th>
                        <th class="px-4 py-2">Office</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($links as $link)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $link->label }}</td>
                            <td class="px-4 py-2">{{ $link->category }}</td>
                            <td class="px-4 py-2">{{ $link->department }}</td>
                            <td class="px-4 py-2">{{ $link->office }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('sharepoint-sites.edit', ['id' => $link->id]) }}"
                                   class="bg-red-900 text-white px-3 py-1 rounded hover:bg-red-700 transition">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
