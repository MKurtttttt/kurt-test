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
                route="portal.loads" 
                icon="images/icons/portal_nav/loads.svg" 
                title="Teaching Loads" 
            />

            <x-navigation.nav-card 
                route="portal.hiring" 
                icon="images/icons/portal_nav/hiring.png" 
                title="Hiring History" 
            />

            <x-navigation.nav-card 
                route="portal.org" 
                icon="images/icons/portal_nav/organization.svg" 
                title="Organization" 
            />

            <x-navigation.nav-card 
                route="portal.respub" 
                icon="images/icons/portal_nav/research.svg" 
                title="Research & Publication" 
            />

            <x-navigation.nav-card 
                route="portal.certifications" 
                icon="images/icons/portal_nav/cert.svg" 
                title="Certifications" 
            />

            <x-navigation.nav-card 
                route="portal.training" 
                icon="images/icons/portal_nav/training.svg" 
                title="Trainings" 
            />

            <x-navigation.nav-card 
                route="portal.license" 
                icon="images/icons/portal_nav/license.png" 
                title="License" 
            />

            <x-navigation.nav-card 
                route="portal.edu-bg" 
                icon="images/icons/portal_nav/education.png" 
                title="Educational Background" 
            />

            <x-navigation.nav-card 
                route="portal.employment" 
                icon="images/icons/portal_nav/employment.png" 
                title="Employment" 
            />

            <x-navigation.nav-card 
                route="portal.dependencies" 
                icon="images/icons/portal_nav/dep.svg" 
                title="Dependents" 
            />

            <x-navigation.nav-card 
                route="portal.pending" 
                icon="images/icons/portal_nav/pending.svg" 
                title="Pending Requests" 
            />

            <x-navigation.nav-card 
                route="portal.filing.type" 
                icon="images/icons/portal_nav/send.svg" 
                title="Filing Application" 
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

</style> 
