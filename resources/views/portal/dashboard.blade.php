<!-- 
    This is the employee dashboard.
    This is different from the dean, hr, and super.
-->

<x-app-layout>
    <div class = "profile_card"> 
        <div class = "profile_card_box"> 
            <div class = "left"> 
                @if(Auth::user()->user->profile_picture)
                <img src ="{{asset ('storage/profile_pictures/' . $userInfo->profile_picture)}}"/>
                @else 
                <img src ="{{asset ('images/blankdp.jpg')}}"/>
                @endif
            </div> 
            <div class = "right"> 
                <div class = "flex flex-col justify-center leading-[1.5rem]">
                    <h3 class = "bg-red-900 text-white font-semibold rounded-lg py-1 px-1 w-[20%] text-center mb-2"> {{Auth::user()-> id}}</h3>
                    <h1 class = "text-[3rem] font-extrabold mb-1">{{$userInfo->emp_fname}}</h1>
                    <h2 class = "text-[1.2rem] font-semibold mt-1">{{$userInfo->emp_mname ?? ' '}} {{$userInfo->emp_lname}} </h2>
                    @if(session('dept')==true)
                    <h3 class = "role">{{ $userInfo->department->dept }} </h3>                    
                    @endif
                </div> 
                <div class = "logo">
                    @if(session('dept')==true)
                        @if($userInfo->department->logo!= '')
                        <img src = "{{asset('storage/dept/logo/'. $userInfo->department->logo)}}"/> 
                        @else
                        <img src="{{asset('images/logo-circle.png')}}" alt="">
                        @endif
                    @else 
                        <img src="{{asset('images/logo-circle.png')}}" alt="">
                    @endif
                </div>  
            </div> 
        </div>         
    </div> 

    <div class="w-full flex justify-center py-4">
        <div class="w-[85%] grid grid-cols-5 gap-4 auto-rows-[200px]">
            
            <x-navigation.nav-card 
                route="portal.profile" 
                icon="images/icons/portal_nav/profile.svg" 
                title="Profile" 
            />
            
            <x-navigation.nav-card 
                route="portal.emp-acad-module" 
                icon="images/icons/portal_nav/emp-acad.svg" 
                title="Employment & Academic Modules" 
            />

            <x-navigation.nav-card 
                route="sharepoint-sites.dashboard" 
                icon="images/icons/nav/sharepoint.png" 
                title="SharePoint Sites" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="information-hub.dashboard" 
                icon="images/icons/nav/information.png" 
                title="Information Hub" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="kpis.dashboard" 
                icon="images/icons/nav/kpi.png" 
                title="KPIs" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="iso.document" 
                icon="images/icons/portal_nav/iso.png" 
                title="ISO Document Handling" 
                :excludedRoles="['Employee', 'Dean', 'HR Admin']"
            />

        </div>
    </div>

    @if(session('show_privacy_modal')) 
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50" id="privacy-modal">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 overflow-hidden">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b">
                <h2 class="text-2xl font-semibold text-gray-800">
                    Data Privacy Notice
                </h2>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 text-gray-600 text-sm leading-relaxed max-h-[400px] overflow-y-auto">
                <p class="mb-3">Your privacy is important to us.</p>

                <p class="mb-3">
                    By proceeding, you acknowledge that the personal information you provide in this system will be collected, processed, and stored by the institution for legitimate administrative and operational purposes. These may include employee record management, institutional reporting, compliance with regulatory requirements, and other official functions of the University.
                </p>

                <p class="mb-3">
                    All personal data will be handled in accordance with the principles of transparency, legitimate purpose, and proportionality as required by the Data Privacy Act of 2012 and the guidelines of the National Privacy Commission.
                </p>

                <p class="mb-3">
                    Your information will only be accessed by authorized personnel and will not be disclosed to third parties without your consent unless required by law.
                </p>

                <p class="mb-3">
                    By marking the checkbox and clicking "Agree”, you confirm that you have read and understood this notice and consent to the processing of your personal data.
                </p>

                <p>
                    For concerns, you may contact the University’s Data Protection Officer.
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t bg-gray-50">
                
                <!-- Checkbox -->
                <div class="flex items-center gap-3 mb-4">
                    <input type="checkbox" id="agree-checkbox" class="w-6 h-6 text-red-900 border-gray-300 rounded focus:ring-red-900 cursor-pointer">
                    <label for="agree-checkbox" class="text-sm font-semibold text-gray-700 cursor-pointer select-none">
                        I have read and agree to the data privacy policy
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3">
                    <button id="decline-btn" 
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                        Decline
                    </button>

                    <button id="agree-btn" 
                        class="px-5 py-2 text-sm font-medium rounded-lg bg-red-900 text-white hover:bg-red-700 disabled:bg-red-300 disabled:cursor-not-allowed transition"
                        disabled>
                        Agree
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('agree-checkbox').addEventListener('change', function() {
            document.getElementById('agree-btn').disabled = !this.checked;
        });

        document.getElementById('agree-btn').addEventListener('click', function() {
            fetch('{{ route("privacy.agree") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json()).then(data => {
                if(data.success) {
                    document.getElementById('privacy-modal').remove();
                }
            });
        });

        document.getElementById('decline-btn').addEventListener('click', function() {
            window.location.href = '{{ route("privacy.decline") }}';
        });
    </script>
    @endif

</x-app-layout>

<style> 
    .profile_card { 
        padding: 1rem 0;
        width: 100%;
        height: 320px; 
        display: flex; 
        justify-content: center;
        align-items: center;
    }

    .profile_card_box { 
        position: relative;

        width: 85%; 
        height: 85%;
        border-radius: 15px;
   
        background-color: white;
        display: grid; 
        grid-template-columns: 30% 70%;
        box-shadow: 5px 5px 5px rgb(0,0,0,0.1);
        overflow: hidden;
    }

    .left, .right { 
        width: 100%; 
        height: 100%; 
    }

    .left { 
        display: flex; 
        justify-content: center;
        align-items: center;
    }

    .left img { 
        width: 200px; 
        height: 200px; 
        border-radius: 50%;
    }

    .right {
        display: grid; 
        grid-template-columns: 65% 30% 5%;
    }

    .profile_info, .logo { 
        width: 100%;
        height: 100%; 
    }

    .profile_info { 
        display: flex; 
        flex-direction: column;
        justify-content: center;
    }

    .main-name{ 
        font-weight: 900;
        font-size: 3rem;
        line-height: 1.5rem;
        color: #333333;
    }

    .sub-name { 
        color: #333333;
        font-size: 2rem;
        text-transform: uppercase;
        line-height: 3.5rem;
        margin-bottom: -0.7rem;
        width: 100%; 
        font-weight: 600;
    }

    .role { 
        padding-left: 0.1rem; 
    }

    .emp-id { 
        margin-bottom: 8px;
        padding: 0.1rem 0.1rem 0.1rem 12px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        display: flex; 
        width: 30%;
        background-color: #70121D;
    }
    
    .logo { 
        display: flex; 
        justify-content: center; 
        align-items: center;
        z-index: 5;
    }

    .logo img { 
        width: 200px; 
        height: 200px;
    }

</style> 
