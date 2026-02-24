<!-- This is the manage employees dashboard for super admin, hr admin, and dean -->
<x-app-layout>
    <div class="min-h-screen">
        <div class="w-full flex justify-center py-8">
            <div class="w-[95%] flex flex-col bg-white rounded-lg px-8 py-8">
                <h1 class="text-[1.8rem] font-semibold">
                    Welcome back, 
                    <strong class="text-red-900"> {{$fname}} </strong>
                </h1>
                <hr class="opacity-100 my-4">

                <div class="w-full flex gap-12">
                    <!-- Management Tools -->
                    <section class="flex flex-col mt-2">
                        <h1 class="text-gray-500 text-[1.7rem] font-bold">Management</h1>
                        <div class="w-full flex py-1 gap-2">
                            <a class="maroon bg-red-900 text-white rounded-[25px] px-12 py-8 font-semibold flex flex-col items-center justify-center" href="{{ route('admin.pendings') }}">
                                <img src="{{ asset('images/icons/portal_nav/pending.png') }}" alt="Pending Requests Icon">
                                <h1>Pending Requests</h1>
                            </a>
                        </div>
                    </section>

                    <!-- Education Tools -->
                    <section class="flex flex-col mt-2">
                        <h1 class="text-gray-500 text-[1.7rem] font-bold">Education</h1>
                        <div class="w-full flex py-1 gap-2">
                            <a class="maroon bg-red-900 text-white rounded-[25px] px-12 py-8 font-semibold flex flex-col items-center justify-center" href="{{ route('admin.loads.db') }}">
                                <img src="{{ asset('images/icons/portal_nav/loads.png') }}" alt="Teaching Loads Icon" class="h-24 w-24">
                                <h1>Teaching Loads</h1>
                            </a>

                            <a class="maroon bg-red-900 text-white rounded-[25px] px-12 py-8 font-semibold flex flex-col items-center justify-center" href="{{ route('admin.subjects') }}">
                                <img src="{{ asset('images/icons/portal_nav/subjects.png') }}" alt="Subjects Icon">
                                <h1>Subjects</h1>
                            </a>
                        </div>
                    </section>
                </div>

                <div class="w-full flex gap-12 mt-4">
                    <!-- Issuance Tools -->
                    <section class="flex flex-col mt-2">
                        <h1 class="text-gray-500 text-[1.7rem] font-bold">Issuance</h1>
                        <div class="w-full flex py-1 gap-2">
                            <a class="maroon bg-red-900 text-white rounded-[25px] px-12 py-8 font-semibold flex flex-col items-center justify-center" href="{{ route('admin.certs') }}">
                                <img src="{{ asset('images/icons/portal_nav/cert.svg') }}" alt="Certifications Icon">
                                <h1>Certificates</h1>
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .maroon {
        transition: 300ms;
    }
    .maroon:hover {
        background-color: #A84655;
    }
</style>
