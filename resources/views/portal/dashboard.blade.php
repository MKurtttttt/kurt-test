<?php
$mname = ''; 

     
?>

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


    <div class = "navigation-cards">
        <div class = "navigation-cards-box-5">

        <!-- a card slot  -->
            <a href = "{{route('portal.profile')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/profile.svg')}}"/> </div>
                    <div class  = "nav-card-title"> <h3>Profile</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->


              <!-- a card slot  -->
              <a href = "{{route('portal.loads')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/loads.svg')}}"/> </div>
                    <div class  = "nav-card-title"> <h3> Teaching Loads</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->

            <!-- a card slot  -->
            <a href = "{{route('portal.hiring')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/hiring.png')}}"/> </div>
                    <div class  = "nav-card-title"> <h3> Hiring History</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->


            <!-- a card slot  -->
            <a href = "{{route('portal.org')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/organization.svg')}}"/> </div>
                    <div class  = "nav-card-title"> <h3> Organizations</h3> </div> 
                </div> 
            </a> 

             <!-- a card slot  -->
             <a href = "{{route('portal.respub')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/research.svg')}}"/> </div>
                    <div class  = "nav-card-title"> <h3> Research & Publication</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->


            <!-- end of card slot  -->

        </div> 
    </div> 
    <div class = "navigation-cards">
        <div class = "navigation-cards-box-5">
            <!-- a card slot  -->
            <a href = "{{route('portal.certifications')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/cert.svg')}}"> </div>
                    <div class  = "nav-card-title"> <h3> Certifications</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->
            <a href = "{{route('portal.training')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/training.svg')}}"/> </div>
                    <div class  = "nav-card-title"> <h3>Trainings</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->


              <!-- a card slot  -->
              <a href = "{{route('portal.license')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/license.png')}}"/> </div>
                    <div class  = "nav-card-title"> <h3> Licenses</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->
            
            <!-- a card slot  -->
            <a href = "{{route('portal.edu-bg')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon w-24 h-24"> <img src = "{{asset('images/icons/portal_nav/education.png')}}"/> </div>
                    <div class  = "nav-card-title"> <h3> Educational Background</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->

            <!-- a card slot  -->
            <a href = "{{route('portal.employment')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/employment.png')}}"/> </div>
                    <div class  = "nav-card-title"> <h3> Employment</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->

        </div> 
    </div> 

    <div class = "navigation-cards">
        <div class = "navigation-cards-box-5">

            <!-- a card slot  -->
            <a href = "{{route('portal.dependencies')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/dep.svg')}}" class = "w-24 h-24"/> </div>
                    <div class  = "nav-card-title"> <h3> Dependents</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->


            <!-- a card slot  -->
            <a href = "{{route('portal.pending')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/pending.svg')}}"/> </div>
                    <div class  = "nav-card-title"> <h3> Pending Requests</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->

            <!-- a card slot  -->
            <a href = "{{route('portal.filing.type')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/send.svg')}}" class = "w-24 h-24"/> </div>
                    <div class  = "nav-card-title"> <h3> Filing Application</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->

            <!-- a card slot  -->
                <a href = "{{route('sharepoint-sites.dashboard')}}">
                    <div class = "nav-card"> 
                        <div class = "nav-card-icon"> <img src = "{{asset('images/icons/nav/sharepoint.png')}}" class = "w-24 h-24"/> </div>
                        <div class  = "nav-card-title"> <h3>SharePoint Sites</h3> </div> 
                    </div> 
                </a> 

            <!-- end of card slot  -->

            <!-- a card slot  -->
                <a href = "{{route('kpis.dashboard')}}">
                    <div class = "nav-card"> 
                        <div class = "nav-card-icon"> <img src = "{{asset('images/icons/nav/kpi.png')}}" class = "w-24 h-24"/> </div>
                        <div class  = "nav-card-title"> <h3>KPIs</h3> </div> 
                    </div> 
                </a> 

            <!-- end of card slot  -->

        </div> 
    </div>
    
    <div class = "navigation-cards">
        <div class = "navigation-cards-box-5">
            <!-- a card slot  -->
            <a href = "{{route('iso.document')}}">
                <div class = "nav-card"> 
                    <div class = "nav-card-icon"> <img src = "{{asset('images/icons/portal_nav/iso.png')}}" class = "w-24 h-24"/> </div>
                    <div class  = "nav-card-title"> <h3> ISO Document Handling</h3> </div> 
                </div> 
            </a> 

            <!-- end of card slot  -->
        </div> 
    </div>

    
  
</x-app-layout>


<style> 
    .profile_card { 
        padding: 1rem 0;
        width: 100%;
        height: 320px; 
        /* border: 1px solid red; */
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
        /* border: 1px solid yellow; */
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
        /* border: 1px solid red; */
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
        /* border: 1px solid red; */
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

    .navigation-cards { 
        width: 100%; 
        height: 250px; 
        /* border: 1px solid yellow; */
        display: flex;
        justify-content: center;
        
    }

    .navigation-cards + .navigation-cards {
        margin-top: -50px; /* Remove any margin between rows */
    }

    .navigation-cards-box-5 { 
        position: relative;

        width: 85%; 
        height: 85%;
        border-radius: 15px;
   
        /* background-color: white; */
        display: grid; 
        grid-template-columns: repeat(5, 1fr); 
        /* box-shadow: 5px 5px 5px rgb(0,0,0,0.1); */
        overflow: hidden;
    }

    .navigation-cards-box-4 { 
        position: relative;

        width: 85%; 
        height: 85%;
        border-radius: 15px;
   
        /* background-color: white; */
        display: grid; 
        grid-template-columns: repeat(4, 1fr); 
        /* box-shadow: 5px 5px 5px rgb(0,0,0,0.1); */
        overflow: hidden;
    }


    .navigation-cards-box a { 
        width: 100%; 
        height: 100%;
        display: flex; 
        justify-content: center;
        align-items: center; 
    }

    .nav-card { 
        /* border: 1px solid red; */
        width: 90%; 
        height: 90%; 
        display: flex; 
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #70121D;
        border-radius: 25px; 
        color: white;
        text-align: center;
        line-height: 1rem;
        transition: 300ms;
    }

    .nav-card:hover { 
        background-color: #8B2B39;
    }



    

</style> 
