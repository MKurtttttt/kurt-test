<div class="sidebar flex h-screen dark:bg-gray-800">
    <!-- This is the admin Sidebar -->
    <aside class="bg-white dark:bg-gray-900 w-64 md:w-80 border-r border-gray-200 dark:border-gray-700 flex-shrink-0 main flex flex-col">
        <div class="w-full flex flex-col items-center mb-4 pt-4 justify-center">
            <img src="{{asset('images/logo-circle.png')}}" alt="" style="width: 150px; height: 150px;" class="w-[80px] h-[80px]" />
            <h1 class="font-bold text-md truncate">Office of Institutional Effectiveness</h1>
        </div>
        <div class = "navigation_links"> 
                <x-navigation.side-nav-card 
                    route="dashboard" 
                    icon="images/icons/nav/dashboard.svg" 
                    title="Dashboard"
                />

                <x-navigation.side-nav-card 
                    route="manage-emps.dashboard" 
                    icon="images/icons/nav/employees.svg" 
                    title="Manage Employees"
                />

                <x-navigation.side-nav-card 
                    route="construction" 
                    icon="images/icons/nav/scholarships.svg" 
                    title="Scholarships and Grants" 
                />

                <x-navigation.side-nav-card 
                    route="construction" 
                    icon="images/icons/nav/outreach.svg" 
                    title="Outreach Programs" 
                />

                <x-navigation.side-nav-card 
                    route="construction" 
                    icon="images/icons/nav/accreditation.svg" 
                    title="Accreditations" 
                />

                <x-navigation.side-nav-card 
                    route="construction" 
                    icon="images/icons/nav/research.svg" 
                    title="Research" 
                />

                <x-navigation.side-nav-card 
                    route="admin.prc" 
                    icon="images/icons/portal_nav/prc.png" 
                    title="PRC Results" 
                    :excludedRoles="['Dean', 'HR Admin']"
                />

                <x-navigation.side-nav-card 
                    route="sharepoint-sites.dashboard" 
                    icon="images/icons/nav/sharepoint.svg" 
                    title="SharePoint Sites" 
                />

                <x-navigation.side-nav-card 
                    route="information-hub.dashboard" 
                    icon="images/icons/nav/information.svg" 
                    title="Information Hub" 
                />

                <x-navigation.side-nav-card 
                    route="kpis.dashboard" 
                    icon="images/icons/nav/kpi.png" 
                    title="KPIs" 
                />

                <x-navigation.side-nav-card 
                    route="visitor-count.dashboard" 
                    icon="images/icons/nav/visitor_count.svg" 
                    title="Visitor Counter" 
                    :excludedRoles="['Dean', 'HR Admin']"
                />
                
                <x-navigation.side-nav-card
                    route="iso.document" 
                    icon="images/icons/portal_nav/iso.png" 
                    title="ISO Document Handling" 
                    :excludedRoles="['Dean', 'HR Admin']"
                />
        </div> 

        <div class="navigation-links">
            <hr>
            <!-- Employee Dashboard (shown to everyone EXCEPT for Employee role and ISO Document Handler) -->
            <x-navigation.side-nav-card 
                route="portal.dashboard" 
                icon="images/icons/portal_nav/profile.svg" 
                title="Employee Dashboard"
            />  

            <!-- Admin Controls (visible to HR Admin and Super Admin only) -->
            <x-navigation.side-nav-card 
                route="admin.dashboard" 
                icon="images/icons/nav/admin-controls.svg" 
                title="Admin Controls" 
                :excludedRoles="['Dean']"
            />

        </div>
    </aside>
</div>

<style> 
    .navigation_links { 
        overflow-y: auto;
        overflow-x: hidden;
    }

    .sidebar{ 
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
    }

    .sidebar-title { 
        display: flex; 
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .sidebar-title h1 { 
        text-transform: uppercase;
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