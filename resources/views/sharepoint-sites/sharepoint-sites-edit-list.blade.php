<x-app-layout>
    <!-- This is where the table of links to choose which one to edit -->
    <div class="p-6">
        @if(session('msg'))
            <div id="success-msg-popup" style="position:fixed;top:90px;left:50%;transform:translateX(-50%);z-index:9999;min-width:300px;max-width:90vw;box-shadow:0 2px 12px rgba(0,0,0,0.15);background:#d1fae5;border:2px solid #10b981;color:#065f46;padding:18px 32px;font-size:1.1rem;border-radius:12px;text-align:center;transition:opacity 0.7s;">
                <strong>Success!</strong> {{ session('msg') }}
            </div>
            <script>
                setTimeout(function() {
                    var msg = document.getElementById('success-msg-popup');
                    if (msg) {
                        msg.style.opacity = '0';
                        setTimeout(function() { msg.style.display = 'none'; }, 700);
                    }
                }, 3000);
            </script>
        @endif
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
                                <div class="flex gap-2">
                                    <a href="{{ route('sharepoint-sites.edit', ['id' => $link->id]) }}"
                                       class="bg-red-900 text-white px-3 py-1 rounded hover:bg-red-700 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('sharepoint-sites.delete', ['id' => $link->id]) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this link: {{ $link->label }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
