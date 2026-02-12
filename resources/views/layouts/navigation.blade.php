<div class="sidebar flex h-screen dark:bg-gray-800">
    <!-- This is the admin Sidebar -->
    <aside class="bg-white dark:bg-gray-900 w-64 md:w-80 border-r border-gray-200 dark:border-gray-700 flex-shrink-0 main flex flex-col">

    <div class="w-full flex flex-col items-center mb-4 pt-4 justify-center">
    <img src="{{asset('images/logo-circle.png')}}" alt="" style="width: 150px; height: 150px;" class="w-[80px] h-[80px]">

        <h1 class="font-bold text-md truncate">Office of Institutional Effectiveness</h1>
        <!-- <h1 class="font-thin text-sm truncate">Institution Database Management Office</h1> -->
    </div>

    <div class = "navigation_links"> 
                <!-- TODO: fix the if statements here to fit on the role available for the ISO Document Handlers and Admin -->
                @if(Auth::user()->role === 'IDC Admin' || Auth::user()->role === 'SuperAdmin' || Auth::user()->role === 'ISO Document Handler')
                <!-- A LINK NAVIGATION  -->
                <a href="{{ route('iso.document') }}" class="{{ request()->routeIs('iso*') ? 'active' : '' }}">
                    <div class="nav-link">
                            <img src="{{ asset('images/icons/portal_nav/iso.png') }}" />
                            <h3>ISO Document Handling</h3>
                    </div>
                </a> 
                @endif 
                @if (Auth::user()->role !== 'ISO Document Handler')
                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                        <div class="nav-link">
                            <img src="{{ asset('images/icons/nav/dashboard.svg') }}" />
                            <h3>Dashboard</h3>
                        </div>
                    </a>
                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('manage-emps.dashboard') }}" class="{{ request()->routeIs('manage-emps*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/employees.svg') }}" />
                            
                                <h3>Manage Employees</h3>
                    
                        </div>
                    </a> 

                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('construction') }}" class="{{ request()->routeIs('scholarships-grants*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/scholarships.svg') }}" />
                            
                                <h3>Scholarships and Grants</h3>
                        
                        </div>
                    </a> 


                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('construction')  }}" class="{{ request()->routeIs('outreach_programs*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/outreach.svg') }}" />
                            
                                <h3>Outreach Programs</h3>
                    
                        </div>
                    </a> 


                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('construction')  }}" class="{{ request()->routeIs('accreditations*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/accreditation.svg') }}" />
                            
                                <h3>Accreditations</h3>
                        
                        </div>
                    </a> 


                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('construction') }}" class="{{ request()->routeIs('research*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/research.svg') }}" />
                            
                                <h3>Research</h3>
                        
                        </div>
                    </a> 
                    
                    @if(Auth::user()->role !== 'Employee' && Auth::user()->role !== 'Dean' && Auth::user()->role !== 'HR Admin' )
                    <a href="{{route('admin.prc')}}" class="{{ request()->routeIs('admin.prc*')? 'active' : ''}}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/portal_nav/prc.png') }}" />
                    
                                <h3>PRC Results</h3>
                    
                        </div>
                    </a>
                    @endif

                    @if(Auth::user()->role !== 'Employee')
                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('sharepoint-sites.dashboard') }}" class="{{ request()->routeIs('sharepoint-sites*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/sharepoint.svg') }}" class = "w-24 h-24"/>
                            
                                <h3>SharePoint Sites</h3>
                        
                        </div>
                    </a>

                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('knowledge-hub.dashboard') }}" class="{{ request()->routeIs('knowledge-hub*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/knowledge.svg') }}" class = "w-24 h-24"/>
                            
                                <h3>Knowledge Hub</h3>
                        
                        </div>
                    </a>

                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('kpis.dashboard') }}" class="{{ request()->routeIs('kpis*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/kpi.png') }}" class = "w-24 h-24"/>
                            
                                <h3>KPIs</h3>
                        
                        </div>
                    </a> 
                    @endif
                    
                    @if(Auth::user()->role !== 'Employee')
                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('visitor-count.dashboard') }}" class="{{ request()->routeIs('visitor-count*') ? 'active' : '' }}">
                        <div class="nav-link">
                            
                                <img src="{{ asset('images/icons/nav/visitor_count.svg') }}" class = "w-24 h-24"/>
                            
                                <h3>Visitor Counter</h3>
                        
                        </div>
                    </a>
                    @endif
                    @elseif(Auth::user()->role === 'ISO Document Handler')
                    <!-- A LINK NAVIGATION  -->
                    <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard*') ? 'font-semibold' : '' }}">
                        <div class="nav-link">        
                            <img src="{{ asset('images/icons/nav/dashboard.svg') }}" />
                            <h3>Dashboard</h3>
                        </div>            
                    </a> 
                @endif
                
    </div> 

    <div class="navigation-links">

    <!-- Employee Dashboard (shown to everyone EXCEPT for Employee role and ISO Document Handler) -->
    @if(Auth::user()->role !== 'Employee' && Auth::user()->role !== 'ISO Document Handler')
        <hr>
       <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard*') ? 'font-semibold' : '' }}">
            <div class="nav-link">        
                <img src="{{ asset('images/icons/portal_nav/profile.svg') }}" />
                <h3>Employee Dashboard</h3>
            </div>            
        </a> 
    @endif

    <!-- Admin Controls (visible to HR Admin and Super Admin only) -->
    @if(Auth::user()->role === 'HR Admin' || Auth::user()->role === 'SuperAdmin')
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.pendings*') ? 'active' : '' }}">
            <div class="nav-link">        
                <img src="{{ asset('images/icons/nav/admin-controls.svg') }}" />
                <h3>Admin Controls</h3>
            </div>            
        </a> 
    @endif
      
    </div>
 
    </aside>
   
</div>

<style> 
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
        height: 100%;
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

    .title h3 { 
        line-height:14px;

        font-weight: bold;
    }

    .title h5 { 
        font-size: clamp(12px, 1.5vw, 14px); 
    }

    .navigation_links  a{
        width: 100%; 
        display: flex; 
        justify-content: center;
        align-items: center;
    }

    .active > .nav-link {
        background-color:#bc0a18;
    }

    .nav-link:hover  { 
        background-color: #8A2B36;
    }

    .nav-link {
        padding: 0rem 0 ; 
        transition: 200ms;
        width: 100%; 
        display: flex;

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