<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 bg-white rounded shadow text-sm text-gray-800">
        <div class="mb-4 flex gap-4 items-center">
            <a href="{{ route('kpis.dashboard') }}" class="inline-flex gap-1 items-center bg-red-900 hover:bg-red-700 px-6 py-1 text-white rounded-xl">
                <img src="{{ asset('images/icons/back.png') }}" class="w-[20px] h-[20px]" alt="">
                <span>Back to KPI Library</span>
            </a>
            <a href="{{ route('kpis.export', $kpi->measure_code) }}" class="inline-flex gap-1 items-center bg-green-600 hover:bg-green-700 px-6 py-1 text-white rounded-xl">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                <span>Export to Excel</span>
            </a>

            @if(Auth::user()->role === 'SuperAdmin')
                <a href="{{ route('kpis.edit', $kpi->measure_code) }}" class="inline-flex gap-1 items-center bg-amber-300 hover:bg-amber-500 px-6 py-1 text-white rounded-xl">
                    <span>Edit</span>
                </a>

                <form :action="`{{ route('kpis.destroy', '') }}/${kpi.measure_code}`" method="POST" @click.stop>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex gap-1 items-center bg-red-600 hover:bg-red-700 px-6 py-1 text-white rounded-xl"
                        onclick="if (!confirm('Are you sure you want to delete this KPI? This action cannot be undone.')) { event.preventDefault(); }">Delete</button>
                </form>
            @endif
        </div>

        {{-- Scorecard --}}
        <h1 class="text-xl font-bold mb-4">Balanced Scorecard KPI Dictionary for {{ $kpi->measure_code }}</h1>
        
        <div class="hidden lg:block">
            <div class="mb-4">
                <div class="grid grid-cols-9 border border-gray-300">
                    {{-- Header --}}
                    <div class="col-span-9 border border-gray-300 bg-gray-100 text-yellow-600 py-2 text-center font-bold">
                        HOLY ANGEL UNIVERSITY<br>
                        BALANCED SCORECARD Performance Measures Information Card
                    </div>
                    
                    {{-- Row 1 --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Perspective</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Measure Code</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->measure_code }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Measure Owner</div>
                    <div class="col-span-2 border border-gray-300 p-2 content-center">{{ $kpi->measure_owner }}</div>
                    
                    {{-- Row 2 --}}
                    <div class="col-span-3 border border-gray-300 p-2 content-center">{{ $kpi->perspective }}</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Measure Name</div>
                    <div class="col-span-5 border border-gray-300 p-2 font-bold content-center">{{ $kpi->measure_name }}</div>
                    
                    {{-- Row 3 and 4 --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Strategic Theme</div>
                    <div class="row-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Description</div>
                    <div class="col-span-5 row-span-2 border border-gray-300 p-2 content-center">{{ $kpi->description }}</div>
                    <div class="col-span-3 border border-gray-300 bg-red-900 text-white px-2 py-10 font-semibold content-center">{{ $kpi->strategic_theme }}</div>
                    
                    {{-- Row 5 --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Objective</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Measure Type</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->measure_type }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Lead/Lag</div>
                    <div class="col-span-2 border border-gray-300 p-2 content-center">{{ $kpi->lead_lag }}</div>

                    {{-- Row 6 and 7 --}}
                    <div class="col-span-3 border border-gray-300 p-2">{{ $kpi->objective }}</div>
                    <div class="row-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Formula</div>                
                    <div class="col-span-5 row-span-2 border border-gray-300 p-2 content-center">{{ $kpi->formula }}</div>
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Objective Owner</div>
                    
                    {{-- Row 8--}}
                    <div class="col-span-3 border border-gray-300 p-2 content-center">{{ $kpi->objective_owner }}</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Unit Type</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->unit_type }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Polarity</div>
                    <div class="col-span-2 border border-gray-300 p-2 content-center">{{ $kpi->polarity }}</div>

                    {{-- Row 9 --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Objective Intended Results</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Data Provider</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->data_provider }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Data Source</div>
                    <div class="col-span-2 border border-gray-300 p-2 content-center">{{ $kpi->data_source }}</div>
                    
                    {{-- Row 10 to 12 --}}
                    <div class="col-span-3 row-span-3 border border-gray-300 p-2 content-center">{{ $kpi->intended_results }}</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Collection Frequency</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->collection_frequency }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Reporting Frequency</div>
                    <div class="col-span-2 border border-gray-300 p-2 content-center">{{ $kpi->reporting_frequency }}</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Verified by</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->verified_by }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Validated by</div>
                    <div class="col-span-2 border border-gray-300 p-2 content-center">{{ $kpi->validated_by }}</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Baseline</div>
                    <div class="col-span-5 border border-gray-300 p-2 text-center">{{ $kpi->baseline }}</div>
                    
                    {{-- Row 13 --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Strategic Initiatives/Action Plans</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Target</div>
                    <div class="col-span-5 border border-gray-300 p-2">{{ $kpi->target }}</div>
                    
                    {{-- Row 14 to 16 --}}
                    <div class="col-span-3 row-span-3 border border-gray-300 p-2 content-center">{{ $kpi->initiatives }}</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Thresholds</div>
                    <div class="border border-gray-300 bg-red-700 text-white text-center p-2"></div>
                    <div class="border border-gray-300 text-center p-2">{{ $kpi->threshold_low }}</div>
                    <div class="border border-gray-300 bg-yellow-400 text-center p-2"></div>
                    <div class="border border-gray-300 text-center p-2">{{ $kpi->threshold_high }}</div>
                    <div class="border border-gray-300 bg-green-500 text-center p-2"></div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Target Rationale</div>
                    <div class="col-span-5 border border-gray-300 p-2 content-center">{{ $kpi->target_rationale }}</div>
                    <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Comparator</div>
                    <div class="col-span-5 border border-gray-300 p-2 content-center">{{ $kpi->comparator }}</div>
                </div>
            </div>
        </div>

        {{-- Mobile Layout --}}
        <div class="block lg:hidden">
            <div class="mb-4">
                <div class="grid grid-cols-6 border border-gray-300">
                    <div class="col-span-6 border border-gray-300 bg-gray-100 text-yellow-600 py-2 text-center font-bold">
                        HOLY ANGEL UNIVERSITY<br>
                        BALANCED SCORECARD Performance Measures Information Card
                    </div>
                    {{-- Perspective --}}
                    <div class="col-span-6 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Perspective</div>
                    <div class="col-span-6 border border-gray-300 p-2 content-center">{{ $kpi->perspective }}</div>

                    {{-- Measure Code and Measure Owner --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Measure Code</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->measure_code }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Measure Owner</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->measure_owner }}</div>

                    {{-- Strategic Theme --}}
                    <div class="col-span-6 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Strategic Theme</div>
                    <div class="col-span-6 border border-gray-300 bg-red-900 text-white px-2 py-10 font-semibold content-center">{{ $kpi->strategic_theme }}</div>

                    {{-- Description --}}
                    <div class="col-span-6 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Description</div>
                    <div class="col-span-6 row-span-2 border border-gray-300 p-2 content-center">{{ $kpi->description }}</div>
                    
                    {{-- Objective --}}
                    <div class="col-span-6 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Objective</div>
                    <div class="col-span-6 border border-gray-300 p-2">{{ $kpi->objective }}</div>

                    {{-- Measure Type --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Measure Type</div>
                    <div class="col-span-3 border border-gray-300 p-2 content-center">{{ $kpi->measure_type }}</div>
                    
                    {{-- Lead/Lag --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Lead/Lag</div>
                    <div class="col-span-3 border border-gray-300 p-2 content-center">{{ $kpi->lead_lag }}</div>

                    {{-- Formula --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Formula</div>                
                    <div class="col-span-4 border border-gray-300 p-2 content-center">{{ $kpi->formula }}</div>

                    {{-- Objective Owner --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Objective Owner</div>
                    <div class="col-span-3 border border-gray-300 p-2 content-center">{{ $kpi->objective_owner }}</div>

                    {{-- Unit Type and Polarity --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Unit Type</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->unit_type }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Polarity</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->polarity }}</div>

                    {{-- Objective Intended Results --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Objective Intended Results</div>
                    <div class="col-span-4 border border-gray-300 p-2 content-center">{{ $kpi->intended_results }}</div>

                    {{-- Data Provider --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Data Provider</div>
                    <div class="col-span-4 border border-gray-300 p-2 content-center">{{ $kpi->data_provider }}</div>

                    {{-- Data Source --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Data Source</div>
                    <div class="col-span-4 border border-gray-300 p-2 content-center">{{ $kpi->data_source }}</div>
                    
                    {{-- Collection Frequency --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Collection Frequency</div>
                    <div class="col-span-4 border border-gray-300 p-2 content-center">{{ $kpi->collection_frequency }}</div>

                    {{-- Reporting Frequency --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Reporting Frequency</div>
                    <div class="col-span-4 border border-gray-300 p-2 content-center">{{ $kpi->reporting_frequency }}</div>

                    {{-- Verified By and Validated By --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Verified by</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->verified_by }}</div>
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Validated by</div>
                    <div class="border border-gray-300 p-2 content-center">{{ $kpi->validated_by }}</div>

                    {{-- Baseline --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Baseline</div>
                    <div class="col-span-4 border border-gray-300 p-2 text-center">{{ $kpi->baseline }}</div>
                    
                    {{-- Strategic Initiatives --}}
                    <div class="col-span-6 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Strategic Initiatives/Action Plans</div>
                    <div class="col-span-6 row-span-3 border border-gray-300 p-2 content-center">{{ $kpi->strategic_initiatives }}</div>
                    
                    {{-- Target --}}
                    <div class="col-span-3 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Target</div>
                    <div class="col-span-3 border border-gray-300 p-2">{{ $kpi->target }}</div>
                    
                    {{-- Threshold --}}
                    <div class="col-span-6 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Thresholds</div>
                    <div class="border border-gray-300 bg-red-700 text-white text-center p-2"></div>
                    <div class="border border-gray-300 text-center p-2">{{ $kpi->threshold_low }}</div>
                    <div class="col-span-2 border border-gray-300 bg-yellow-400 text-center p-2"></div>
                    <div class="border border-gray-300 text-center p-2">{{ $kpi->threshold_high }}</div>
                    <div class="border border-gray-300 bg-green-500 text-center p-2"></div>

                    {{-- Target Rationale --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Target Rationale</div>
                    <div class="col-span-4 border border-gray-300 p-2 content-center">{{ $kpi->target_rationale }}</div>

                    {{-- Comparator --}}
                    <div class="col-span-2 border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Comparator</div>
                    <div class="col-span-4 border border-gray-300 p-2 content-center">{{ $kpi->comparator }}</div>
                </div>
            </div>
        </div>

        {{-- SEGMENTATION TABLE --}}
        @if($kpi->segmentations && $kpi->segmentations->count())
            @php
                $hasTargetLevel = $kpi->segmentations->contains(fn($seg) => !is_null($seg->target_level));
                $hasGoal = $kpi->segmentations->contains(fn($seg) => !is_null($seg->goal));
            @endphp

            <div class="mt-6 mb-4">
                <table class="table-fixed border-2 border-gray-300 w-full text-sm">
                    <thead class="bg-gray-100 text-yellow-600">
                        <tr>
                            <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/4">Segmentation</th>
                            <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/6">Code</th>
                            <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/6">Owner</th>
                            @if ($hasTargetLevel)
                                <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/6">Target Level</th>
                            @endif
                            @if ($hasGoal)
                                <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/4">Goal</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kpi->segmentations as $seg)
                            <tr>
                                <td class="border-2 border-gray-300 px-4 py-2 w-1/4">{{ $seg->segmentation }}</td>
                                <td class="border-2 border-gray-300 px-4 py-2 w-1/6">{{ $seg->code }}</td>
                                <td class="border-2 border-gray-300 px-4 py-2 w-1/6">{{ $seg->owner }}</td>
                                @if ($hasTargetLevel)
                                    <td class="border-2 border-gray-300 px-4 py-2 w-1/6">
                                        {{ $seg->target_level ?? '-' }}
                                    </td>
                                @endif
                                @if ($hasGoal)
                                    <td class="border-2 border-gray-300 px-4 py-2 w-1/4">
                                        {{ $seg->goal ?? '-' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="grid grid-cols-4 border ">
            <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Item Author</div>
            <div class="border border-gray-300 content-center p-2">{{ $kpi->item_author }}</div>
            <div class="border border-gray-300 bg-gray-100 text-yellow-600 p-2 font-semibold content-center">Date</div>
            <div class="border border-gray-300 content-center p-2">{{ $kpi->date }}</div>
        </div>

        {{-- ACCREDITING BODY TABLE --}}
        @if($kpi->accreditations && $kpi->accreditations->count())
            <div class="mt-6">
                <table class="table-auto border border-gray-300 w-full text-sm">
                    <thead class="bg-gray-100 text-yellow-600">
                        <tr>
                            <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/3">Accrediting Body ID</th>
                            <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/3">Accrediting Body Name</th>
                            <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/3">Program Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kpi->accreditations as $acc)
                            <tr>
                                <td class="border-2 border-gray-300 px-4 py-2">{{ $acc->accrediting_body_id }}</td>
                                <td class="border-2 border-gray-300 px-4 py-2">{{ $acc->accrediting_body_name }}</td>
                                <td class="border-2 border-gray-300 px-4 py-2">{{ $acc->program_unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- DIMENSIONS TABLE --}}
        @if($kpi->dimensions && $kpi->dimensions->count())
            <div class="mt-6">
                <table class="table-auto border-2 border-gray-300 w-full text-sm">
                    <thead class="bg-gray-100 text-yellow-600">
                        <tr>
                            <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/3">Dimensions</th>
                            <th class="border-2 border-gray-300 px-4 py-2 text-left w-1/3">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kpi->dimensions as $dim)
                            <tr>
                                <td class="border-2 border-gray-300 px-4 py-2">{{ $dim->dimensions }}</td>
                                <td class="border-2 border-gray-300 px-4 py-2">{{ $dim->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <x-back-top-button />
    
</x-app-layout>
