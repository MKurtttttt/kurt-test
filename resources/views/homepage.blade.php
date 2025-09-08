@extends('layouts.main')

@section('title', 'Office of Institutional Effectiveness')

@section('content')
    <!-- Image Banner -->
    <div class="relative" style="background-image: url('{{ asset('images/hau-bg.png') }}'); background-size: cover; background-position: center; height: 16rem; min-height: 256px; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.4); display: flex; align-items: center; justify-content: center;">
            <div style="text-align: center; color: white;">
                <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem; color: white;">
                    Office of Institutional Effectiveness
                </h1>
                <p style="font-size: 1.25rem; opacity: 0.9; color: white;">
                    Institutional Database Management Office
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-6xl mx-auto px-4 py-12">
        <!-- Welcome Message -->
        <div class="bg-red-50 border-l-4 border-red-600 p-6 mb-12 rounded-r-lg">
            <h2 class="text-xl font-semibold text-red-800 mb-2">Welcome to the Institutional Database Portal</h2>
            <p class="text-red-700"></p>
        </div>

        <!-- Quick Access Grid -->
        <div class="mb-16">
            <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Quick Access</h3>
            <div class="max-w-xl mx-auto">
                <!-- SharePoint Card -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow mb-8">
                    <div class="bg-blue-600 px-6 py-4">
                        <h4 class="text-white font-semibold text-lg">SharePoint Resources</h4>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4"></p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">ISO Documentation</span>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">Planning & Review</span>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">Quality Assurance</span>
                        </div>
                        <a href="{{ route('sharepoint.public') }}" class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                            Browse SharePoint Sites
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Section -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="bg-red-600 px-6 py-4">
                <h4 class="text-white font-semibold text-lg">Full Dashboard Access</h4>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-4 md:mb-0">
                        <h5 class="font-semibold text-gray-900 mb-2"></h5>
                        <p class="text-gray-600"></p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('login') }}" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition-colors font-medium">
                            Login to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


