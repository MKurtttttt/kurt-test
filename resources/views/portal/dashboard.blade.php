<!-- 
    This is the employee dashboard.
    This is different from the dean, hr, and super.
-->

<x-app-layout>
    <div class = "profile_card"> 
        <div class = "profile_card_box">
            <div class = "square"> </div> 
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
                        <img src="{{asset('images/hau-logo.png')}}" alt="">
                        @endif
                    @else 
                        <img src="{{asset('images/hau-logo.png')}}" alt="">
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
                route="portal.loads" 
                icon="images/icons/portal_nav/loads.svg" 
                title="Teaching Loads" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.hiring" 
                icon="images/icons/portal_nav/hiring.png" 
                title="Hiring History" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.org" 
                icon="images/icons/portal_nav/organization.svg" 
                title="Organization" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.respub" 
                icon="images/icons/portal_nav/research.svg" 
                title="Research & Publication" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.certifications" 
                icon="images/icons/portal_nav/cert.svg" 
                title="Certifications" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.training" 
                icon="images/icons/portal_nav/training.svg" 
                title="Trainings" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.license" 
                icon="images/icons/portal_nav/license.png" 
                title="License" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.edu-bg" 
                icon="images/icons/portal_nav/education.png" 
                title="Educational Background" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.employment" 
                icon="images/icons/portal_nav/employment.png" 
                title="Employment" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.dependencies" 
                icon="images/icons/portal_nav/dep.svg" 
                title="Dependents" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.pending" 
                icon="images/icons/portal_nav/pending.svg" 
                title="Pending Requests" 
                :excludedRoles="['ISO Document Handler']"
            />

            <x-navigation.nav-card 
                route="portal.filing.type" 
                icon="images/icons/portal_nav/send.svg" 
                title="Filing Application" 
                :excludedRoles="['ISO Document Handler']"
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
    .square{ 
        z-index: 1;
        position: absolute;
        background-color: #70121D;
        width: 180px; 
        height: 180px;
        rotate: 45deg;
        left: 90%;
        top: 2rem;
    }

</style> 
