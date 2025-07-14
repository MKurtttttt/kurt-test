<div class="sidebar h-screen dark:bg-gray-800">
    <!-- Sidebar for employees / employee sidebar -->
    <aside class="bg-white dark:bg-gray-900 w-64 md:w-80 border-r border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center flex-shrink-0 main gap-0 leading-tight">

    <div class = "w-full flex flex-col items-center mt-8"> 
        <h1 class="mb-2 font-extrabold text-xl">HAU-IDMO Portal</h1>

        <!-- profile card -->

        <div class="w-[90%] grid grid-cols-[20%_80%] bg-red-800 rounded-md py-2 px-1 text-white border border-white border-opacity-10">
                <div class="flex justify-center items-center">
                    @if(Auth::user()->user->profile_picture == '')
                    <img src = "{{asset('images/blankdp.jpg')}}" class="w-[35px] h-[35px] rounded-xl ">
                    
                    @else
                    <img src = "{{asset('storage/profile_pictures/' . Auth::user()->user->profile_picture)}}" class="w-[35px] h-[35px] rounded-full">
                    @endif
                </div>

                <div class="w-full flex flex-col justify-start items-start">
                    <h1 class="font-bold text-sm break-words">{{ Auth::user()->user->emp_lname . ', ' . Auth::user()->user->emp_fname . ' ' . Auth::user()->user->emp_mname }}</h1>
                    <h1 class="text-gray-100 text-[0.8rem] break-words">{{ Auth::user()->email }}</h1>
                </div>


            </div>

        </div> 


    <hr class="w-[90%] rounded-xl opacity-1 my-4">

    <div class = "navigation_links flex flex-col items-center py-2"> 

                <!-- A LINK NAVIGATION  -->
                <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                       
                            <img src="{{ asset('images/icons/nav/dashboard.svg') }}" />
                     
                     
                            <h3>Dashboard</h3>
            
                    </div>
                </a> 

                <a href="{{ route('portal.profile') }}" class="{{ request()->routeIs('portal.profile*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                       
                            <img src="{{ asset('images/icons/portal_nav/profile.svg') }}" />
                     
                     
                            <h3>Profile</h3>
            
                    </div>
                </a> 

                   <!-- A LINK NAVIGATION  -->
                   <a href="{{ route('portal.loads') }}" class="{{ request()->routeIs('portal.loads*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                       
                            <img src="{{ asset('images/icons/portal_nav/loads.svg') }}" />
                     
                     
                            <h3>Teaching Loads</h3>
            
                    </div>
                </a> 

                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('portal.hiring') }}" class="{{ request()->routeIs('portal.hiring*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                       
                            <img src="{{ asset('images/icons/portal_nav/hiring.png') }}" />
                     
                     
                            <h3>Hiring History</h3>
            
                    </div>
                </a> 




                <!-- A LINK NAVIGATION  -->
                <a href="{{ route('portal.org') }}" class="{{ request()->routeIs('portal.org*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/organization.svg') }}" />
                    
                     
                            <h3>Organizations</h3>
                    
                    </div>
                </a> 



                 <!-- A LINK NAVIGATION  -->
                 <a href="{{ route('portal.respub') }}" class="{{ request()->routeIs('portal.respub*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/research.svg') }}" />
                       
                     
                            <h3>Research & Publication</h3>
                        
                    </div>
                </a> 


              

                
              

                <a href="{{ route('portal.certifications') }}" class="{{ request()->routeIs('portal.certifications*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/cert.svg') }}" />
                    
                            <h3> Certifications </h3>
                      
                    </div>
                </a> 

        



                
                <a href="{{ route('portal.training') }}" class="{{ request()->routeIs('portal.training*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/training.svg') }}" />
                
                     
                            <h3> Trainings </h3>
                  
                    </div>
                </a> 


                <a href="{{ route('portal.license') }}" class="{{ request()->routeIs('portal.license*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/license.png') }}" />
                    
                            <h3>Licenses</h3>
                      
                    </div>
                </a> 

                
                <a href="{{ route('portal.edu-bg') }}" class="{{ request()->routeIs('portal.edu-bg') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/education.png') }}" />
                    
                            <h3> Educational Background</h3>
                      
                    </div>
                </a> 



                <a href="{{ route('portal.employment') }}" class="{{ request()->routeIs('portal.employment*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/employment.png') }}" />
                    
                            <h3> Employment</h3>
                      
                    </div>
                </a> 

                   <!-- A LINK NAVIGATION  -->
                   <a href="{{ route('portal.dependencies') }}" class="{{ request()->routeIs('portal.dependencies*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/dep.svg') }}" />
                   
                            <h3>Dependents </h3>
                        
                    </div>
                </a> 

                
                 <!-- A LINK NAVIGATION  -->
                 <a href="{{ route('portal.pending') }}" class="{{ request()->routeIs('portal.pending*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/pending.svg') }}" />
                 
                            <h3>Pending Requests</h3>


                        
                  
                    </div>
                </a> 


                 <!-- A LINK NAVIGATION  -->
                 <a href="{{ route('portal.filing.type') }}" class="{{ request()->routeIs('portal.filing*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/portal_nav/send.svg') }}" />
                    
                     
                            <h3>Filing Application</h3>
                     
                    </div>
                </a>

                <!-- A LINK NAVIGATION  -->
                 <a href="{{ route('sharepoint-sites.dashboard') }}" class="{{ request()->routeIs('sharepoint-sites*') ? 'font-semibold' : '' }}">
                    <div class="nav-link">
                        
                            <img src="{{ asset('images/icons/nav/sharepoint.svg') }}" />
                    
                     
                            <h3>SharePoint Sites</h3>
                     
                    </div>
                </a> 

         @if(Auth::user()->role !== 'Employee')
        <!-- A LINK NAVIGATION  -->
        <div class="w-full flex justify-center my-4">
            <a href="{{route('dashboard')}}" class="w-[90%] flex items-center justify-center px-4 py-1 gap-2 bg-white hover:bg-gray-200 rounded-xl">
            <img src="{{asset('images/icons/back_maroon.png')}}" alt="" class="w-[20px] h-[20px">
                <span class="text-red-900 font-semibold">Back to Main Dashboard</span>
            </a>
        </div>
        @endif



              

    </div> 
   


   
    </div>

    
    
    
    </aside>

   
</div>


<style> 


@media only screen and (max-width: 1024px) {
    .title { 
        display: flex; 
        justify-content: center;
        align-items: end;
    }

    .title h3, .title h5 { 
        display: none;
    }

    .nav-link h3 { 
        font-size: 0.7rem;
    }
}


@media only screen and (max-width: 768px) {
    .nav-link h3 { 
        display: none;
    }

    .nav-link { 
        display: flex;
        align-items: center;
        justify-content: center;
    }
}


.navigation_links { 


    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar{ 
    /* border: 1px solid red; */
    width: 100%;
    color: white;
} 


.main {
    width: 20%; 
    height: 100%;  
    background-color: #70121D;
}    



hr { 
    opacity: 0.3;
    color: lightgray; 
    text-align: center;
}

.b2d { 
    background-color: #a0202a;
    display: flex; 
    justify-content: center;
    align-items: center;
    width: 100%; 
    padding: 1rem 0;
}

.b2d a { 
    width: 90%; 
    height: 80%; 
    
}

.b2d .nav-link:hover { 
    background-color: #871c25;
}

.b2d .nav-link{
   
    width: 100%; 
    height: 100%;
    display: grid; 
    grid-template-columns: 20% 80%;
 }

 .b2d .nav-icon { 
    justify-content: end;
    padding-right: 0.5rem;
 }



.title, .navigation_links, .sidebar-title{ 
    width: 100%;
    height: 100%; 
    /* border: 1px solid yellow; */
}

.sidebar-title { 
    display: flex; 
    justify-content: center;
    align-items: center;
    text-align: center;
}

.sidebar-title h1 { 
    text-transform: uppercase;
    /* font-style: italic; */
    /* font-style: oblique; */
    font-weight: bold;
    font-size: clamp(0.8rem, 2vw + 0.5rem, 1.2rem);
    margin-bottom: 1rem;
}

.title { 
    display: flex; 
    flex-direction: column;
    align-items: center;
    padding: 1rem 0;
}


.title img { 
    width: 145px; 
    height: 145px;
}

.title img { 
    width: 145px; 
    height: 145px;
}



.navigation_links  a{

    width: 90%; 

    display: flex; 
    justify-content: center;
    align-items: center;
}

.font-semibold > .nav-link {
    background-color:#bc0a18;
    font-weight: 700;
}


#total { 
    background-color: white; 
    color: maroon;
    font-weight: 700;
    padding: 0.1rem 0.6rem;
    border-radius: 10px;
    margin-left: 0.3rem;
}



.nav-link:hover  { 
    background-color: #8A2B36;
}

.nav-link { 

    transition: 200ms;
    width: 100%; 
    display: flex;

    border-radius: 5px;
    align-items: center;
}

.nav-link h3 { 
    font-size: 1rem;
}

.nav-link img { 
 
    width: 25px; 
    margin: 0.7rem;
    height: 25px; 
}



.change_dp button { 
    margin: 1rem 0;
    background-color: #70121D;
    padding: 5px 10px;
    font-size: 12px;
    color: white;
    border-radius: 15px;
}

.change_dp img { 
    width: 200px; 
    height: 200px;
    border-radius: 15px;
}
</style> 
