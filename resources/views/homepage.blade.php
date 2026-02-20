@extends('layouts.main')

@section('title', 'Office of Institutional Effectiveness')

@section('content')
    <!-- Background Image -->
    <div style="background-image: url('{{ asset('images/hau-lib.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.3;"></div>
    
    <!-- Image Banner -->
    <div class="relative" style="background-image: url('{{ asset('images/hau-bg.png') }}'); background-size: cover; background-position: center; height: 16rem; min-height: 256px; overflow: hidden; position: relative; z-index: 1;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.4); display: flex; align-items: center; justify-content: center;">
            <div style="text-align: center; color: white;">
                <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem; color: white;">
                    Office of Institutional Effectiveness
                </h1>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-6xl mx-auto px-4 py-12" style="position: relative; z-index: 1; margin-top: 2rem;">
        <!-- Welcome Message -->
    <div class="border-l-4 p-6 rounded-r-lg" style="background-color: #FFF8DC; border-left-color: #B8860B; max-width: 900px; width: 100%; margin-left: auto; margin-right: auto; margin-bottom: 2.5rem;">
            <h2 class="text-xl font-semibold mb-2" style="color: #70121D;">Welcome to the Office of Institutional Effectiveness</h2>
            <div class="flex flex-row">
                <img src="{{ asset('images/icons/eye-2.svg') }}" class = "w-6 h-6"/>
                <div class="px-2">
                    {{ $count }} {{ ucfirst($period) }} Visitors               
                </div>
            </div>
            <p style="color: #B8860B;"></p>
        </div>

        <!-- Quick Access Grid -->
        <div class="mb-16">
            <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Quick Access</h3>
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch justify-center">
                    <!-- SharePoint Card -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow mb-0 h-full flex flex-col" style="width: 100%;">
                        <div class="px-6 py-4" style="background: linear-gradient(135deg, #70121D 0%, #B8860B 100%);">
                            <h4 class="text-white font-semibold text-lg">SharePoint Resources</h4>
                        </div>
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-4">Access SharePoint links on ISO, Planning and Review, and Quality Assurance.</p>
                            </div>
                            <a href="{{ route('sharepoint.public') }}" class="inline-flex items-center text-white rounded transition-colors" style="background: linear-gradient(135deg, #70121D 0%, #B8860B 100%); hover:opacity: 0.9; padding: 0.375rem 0.75rem; font-size: 0.95rem; min-width: 110px;">
                                Browse SharePoint Sites
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Org Chart Card -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow mb-0 h-full flex flex-col" style="width: 100%;">
                        <div class="px-6 py-4" style="background: linear-gradient(135deg, #70121D 0%, #B8860B 100%);">
                            <h4 class="text-white font-semibold text-lg">Organizational Chart</h4>
                        </div>
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-4">View the OIE's organizational structure and team hierarchy.</p>
                            </div>
                            <a href="{{ route('orgchart') }}" class="inline-flex items-center text-white rounded transition-colors" style="background: linear-gradient(135deg, #70121D 0%, #B8860B 100%); hover:opacity: 0.9; padding: 0.375rem 0.75rem; font-size: 0.95rem; min-width: 110px;">
                                View Org Chart
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Information Hub Card -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow mb-0 h-full flex flex-col md:col-span-2 md:max-w-md md:mx-auto" style="width: 100%;">
                        <div class="px-6 py-4" style="background: linear-gradient(135deg, #70121D 0%, #B8860B 100%);">
                            <h4 class="text-white font-semibold text-lg">Information Hub Resources</h4>
                        </div>
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-4">Access Information Hub</p>
                            </div>
                            <a href="{{ route('information-hub.public') }}" class="inline-flex items-center text-white rounded transition-colors" style="background: linear-gradient(135deg, #70121D 0%, #B8860B 100%); hover:opacity: 0.9; padding: 0.375rem 0.75rem; font-size: 0.95rem; min-width: 110px;">
                                Browse Information Hub
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Section (Smaller) -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden mt-8" style="max-width: 370px; width: 100%; margin-left: auto; margin-right: auto;">
            <div class="px-4 py-2" style="background: linear-gradient(135deg, #70121D 0%, #B8860B 100%);">
                <h4 class="text-white font-semibold text-base">Full Dashboard Access</h4>
            </div>
            <div class="p-4">
                <div class="flex flex-col items-center text-center">
                    <p class="text-gray-600 text-sm mb-3">Login to access advanced features.</p>
                    <a href="{{ route('login') }}" class="text-white px-4 py-1.5 rounded font-medium text-sm transition-colors" style="background: linear-gradient(135deg, #70121D 0%, #B8860B 100%); min-width: 140px;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Login to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection


