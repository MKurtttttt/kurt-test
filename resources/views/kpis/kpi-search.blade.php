@php
function highlightText($text, $search) {
    if (empty($search) || empty($text)) {
        return e($text);
    }
    
    $highlighted = preg_replace(
        '/(' . preg_quote($search, '/') . ')/i',
        '<mark class="bg-yellow-200 text-gray-900 px-1 rounded">$1</mark>',
        e($text)
    );
    
    return $highlighted;
}
@endphp

<div class="min-h-screen bg-transparent flex flex-col gap-6 mt-2">
    @forelse($kpis as $kpi)
        <div class="bg-white rounded-xl shadow-lg p-6 flex flex-row items-center justify-between hover:shadow-xl transition border border-red-100 cursor-pointer" onclick="window.location='{{ route('kpis.show', $kpi->measure_code) }}'">
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-red-700 mb-1 truncate">
                    <a href="{{ route('kpis.show', $kpi->measure_code) }}" class="hover:underline">{!! highlightText($kpi->measure_name, $search ?? '') !!}</a>
                </h2>
                <p class="text-gray-600 text-sm mb-2 truncate">
                    {{ Str::limit($kpi->description, 200) }}
                </p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="bg-red-50 text-red-700 px-2 py-1 rounded text-xs font-semibold">{!! highlightText($kpi->objective, $search ?? '') !!}</span>
                    <span class="bg-red-100 text-gray-700 px-2 py-1 rounded text-xs font-semibold">{!! highlightText($kpi->strategic_theme, $search ?? '') !!}</span>
                    <span class="bg-gray-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">{!! highlightText($kpi->perspective, $search ?? '') !!}</span>
                </div>
                <div class="text-xs text-gray-400 mb-2">Code: {!! highlightText($kpi->measure_code, $search ?? '') !!} | Owner: {{ $kpi->measure_owner }}</div>
            </div>
            @if(Auth::user()->role === 'SuperAdmin')
                <div class="flex flex-col gap-2 items-end ml-6">
                    <a href="{{ route('kpis.edit', $kpi->measure_code) }}"
                       class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow transition w-16 text-center">Edit</a>
                    <form action="{{ route('kpis.destroy', $kpi->measure_code) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this KPI?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-xs font-semibold shadow transition w-16">Delete</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="min-h-[60vh] flex items-center justify-center">
            <div class="bg-transparent rounded-xl shadow p-6 text-400 italic text-center border border-red-100">
                No KPIs found.
            </div>
        </div>
    @endforelse
</div>
