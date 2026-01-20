<x-app-layout>
    <!-- KPI Add Page -->
    <div class="min-h-screen">
        <div class="flex justify-center items-center w-full py-8">
            <div class="flex-col w-[95%] bg-white rounded-lg py-8">
                <div class="px-8 pb-4">
                    <a href="{{ route('kpis.dashboard') }}" class="inline-flex gap-1 items-center bg-red-900 hover:bg-red-700 px-6 py-1 text-white rounded-xl">
                        <img src="{{ asset('images/icons/back.png') }}" class="w-[20px] h-[20px]" alt="">
                        <span>Return to KPI Dashboard</span>
                    </a>
                </div>
                <div class="w-full flex flex-col items-center justify-center px-8 py-4 leading-tight">
                    <img class="w-[120px] h-[120px] my-0" src="{{ asset('images/logo-circle.png') }}" />
                    <h1 class="text-[3rem] font-bold text-gray-700">ADD KPI</h1>
                    <span class="text-[0.7rem] text-gray-400">
                        Please fill out the KPI information accurately.
                    </span>
                </div>

                <form class="flex-col w-full px-8" action="{{ route('kpis.store') }}" method="POST">
                    @csrf

                    <!-- Row 1: Measure Code & Measure Owner -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">MEASURE CODE <span class="font-bold text-red-500">*</span></h1>
                            <input class="rounded-lg w-full" name="measure_code" type="text" required/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">MEASURE OWNER <span class="font-bold text-red-500">*</span></h1>
                            <input class="rounded-lg w-full" name="measure_owner" type="text" required/>
                        </div>
                    </div>

                    <!-- Row 2: Measure Name & Measure Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">MEASURE NAME <span class="font-bold text-red-500">*</span></h1>
                            <input class="rounded-lg w-full" name="measure_name" type="text" required/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">MEASURE TYPE <span class="font-bold text-red-500">*</span></h1>
                            <input class="rounded-lg w-full" name="measure_type" type="text" required/>
                        </div>
                    </div>

                    <!-- Row 3: Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">CATEGORY <span class="font-bold text-red-500">*</span></h1>
                            <select class="rounded-lg w-full" name="category" required>
                                <option value="">Select Category</option>
                                <option value="departmental">Departmental</option>
                                <option value="institutional">Institutional</option>
                                <option value="personnel">Personnel</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Description -->
                    <div class="mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">DESCRIPTION</h1>
                            <textarea class="rounded-lg w-full" name="description" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Row 5: Lead/Lag & Polarity -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">LEAD/LAG</h1>
                            <select class="rounded-lg w-full" name="lead_lag">
                                <option value="">Select</option>
                                <option value="Lead">Lead</option>
                                <option value="Lag">Lag</option>
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">POLARITY</h1>
                            <select class="rounded-lg w-full" name="polarity">
                                <option value="">Select</option>
                                <option value="Positive">></option>
                                <option value="Negative"><</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 6: Formula & Unit Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">FORMULA</h1>
                            <input class="rounded-lg w-full" name="formula" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">UNIT TYPE</h1>
                            <input class="rounded-lg w-full" name="unit_type" type="text"/>
                        </div>
                    </div>

                    <!-- Row 7: Data Provider & Data Source -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">DATA PROVIDER</h1>
                            <input class="rounded-lg w-full" name="data_provider" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">DATA SOURCE</h1>
                            <input class="rounded-lg w-full" name="data_source" type="text"/>
                        </div>
                    </div>

                    <!-- Row 8: Collection Frequency & Reporting Frequency -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">COLLECTION FREQUENCY</h1>
                            <input class="rounded-lg w-full" name="collection_frequency" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">REPORTING FREQUENCY</h1>
                            <input class="rounded-lg w-full" name="reporting_frequency" type="text"/>
                        </div>
                    </div>

                    <!-- Row 9: Verified By & Validated By -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">VERIFIED BY</h1>
                            <input class="rounded-lg w-full" name="verified_by" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">VALIDATED BY</h1>
                            <input class="rounded-lg w-full" name="validated_by" type="text"/>
                        </div>
                    </div>

                    <!-- Row 10: Baseline & Target -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">BASELINE</h1>
                            <input class="rounded-lg w-full" name="baseline" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">TARGET</h1>
                            <input class="rounded-lg w-full" name="target" type="text"/>
                        </div>
                    </div>

                    <!-- Row 11: High Threshold & Low Threshold -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">HIGH THRESHOLD</h1>
                            <input class="rounded-lg w-full" name="threshold_high" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">LOW THRESHOLD</h1>
                            <input class="rounded-lg w-full" name="threshold_low" type="text"/>
                        </div>
                    </div>

                    <!-- Row 12: Target Rationale -->
                    <div class="mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">TARGET RATIONALE</h1>
                            <textarea class="rounded-lg w-full" name="target_rationale" rows="3"></textarea>
                        </div>
                    </div>
                     
                    <!-- Row 13: Perspective & Strategic Theme -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">PERSPECTIVE</h1>
                            <input class="rounded-lg w-full" name="perspective" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">STRATEGIC THEME</h1>
                            <input class="rounded-lg w-full" name="strategic_theme" type="text"/>
                        </div>
                    </div>

                    <!-- Row 14: Objective & Objective Owner -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">OBJECTIVE</h1>
                            <input class="rounded-lg w-full" name="objective" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">OBJECTIVE OWNER</h1>
                            <input class="rounded-lg w-full" name="objective_owner" type="text"/>
                        </div>
                    </div>

                    <!-- Row 15: Strategic Initiatives -->
                    <div class="mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">STRATEGIC INITIATIVES</h1>
                            <textarea class="rounded-lg w-full" name="strategic_initiatives" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Row 16: Intended Results -->
                    <div class="mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">INTENDED RESULTS</h1>
                            <textarea class="rounded-lg w-full" name="intended_results" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Row 17: Comparator & Item Author -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">COMPARATOR</h1>
                            <input class="rounded-lg w-full" name="comparator" type="text"/>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">ITEM AUTHOR</h1>
                            <input class="rounded-lg w-full" name="item_author" type="text"/>
                        </div>
                    </div>

                    <!-- Row 18: Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <h1 class="text-gray-500">DATE</h1>
                            <input class="rounded-lg w-full" name="date" type="date"/>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="w-full flex justify-end py-4">
                        <button class="maroon text-white px-12 py-2 rounded-md" type="submit">ADD KPI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-back-top-button />
</x-app-layout>

<style>
    .maroon {
        background-color: maroon;
    }
</style>
