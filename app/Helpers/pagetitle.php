<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class pagetitle
{
    public static function getCurrentPageTitle()
    {
        $curr_page = '';
        switch (Route::currentRouteName()) {
            //app-nav
            case 'dashboard':
                $curr_page = 'Dashboard';
                break;
            case 'profile.edit':
                $curr_page = 'Settings';
                break;
            case 'profile.changepic':
                $curr_page = 'Settings';
                break;

            //portal-nav
            case 'portal.profile':
                $curr_page = 'Profile';
                break;
            case 'portal.profile-edit-pd':
                $curr_page = 'Edit - Personal Data';
                break;
            case 'portal.profile-edit-ci':
                $curr_page = 'Edit - Contact Information';
                break;
            case 'portal.profile-edit-pc':
                $curr_page = 'Edit - Provincial Contact';
                break;
            case 'portal.profile-edit-ad':
                $curr_page = 'Edit - Accounting Details';
                break;
            case 'portal.profile-edit-ei':
                $curr_page = 'Edit - Emergency Information';
                break;

            //pages of Portal
            case 'portal.dependencies':
            case 'portal.dependencies.search':
            case 'portal.dependencies.add':
                $curr_page = 'Dependents';
                break;

            case 'portal.pending':
                $curr_page = 'Pending Requests';
                break;

            case 'portal.respub':
            case 'portal.respub.research':
            case 'portal.respub.publications':
            case 'portal.respub.type':
            case 'portal.respub.research': 
            case 'portal.respub.publication': 
            case 'portal.respub.approved': 
            case 'portal.respub.pending': 
            case 'portal.respub.toreview': 
                $curr_page = 'Research and Publications';
                break;
            case 'portal.respub.add.research':
                $curr_page = 'Add - Research';
                break;
            case 'portal.respub.add.publication':
                $curr_page = 'Add - Publication';
                break;

            case 'portal.pending': 
            case 'portal.pending.view': 
                $curr_page = 'Pending Requests';
                break;
            //filtering for pending requests
            case 'portal.pending.respub': 
                $curr_page = 'Research and Publications - Pending Requests'; 
                break; 
            case 'portal.pending.certification': 
                $curr_page = 'Certifications - Pending Requests'; 
                break; 

            case 'portal.pending.training': 
                $curr_page = 'Trainings - Pending Requests'; 
                break; 
            case 'portal.pending.license': 
                $curr_page = 'License - Pending Requests'; 
                break; 
            case 'portal.pending.employment': 
                $curr_page = 'Employment - Pending Requests'; 
                break; 


            case 'portal.certifications': 
            case 'portal.certifications.approved': 
            case 'portal.certifications.pending': 
            case 'portal.certifications.toreview': 
                $curr_page = 'Certifications'; 
                break;
                
            case 'portal.certifications.view':  
                $curr_page = 'View Certification'; 
                break; 

            case 'portal.certifications.edit': 
                $curr_page  = 'Edit Certification';
                break; 
            
            
            // DASHBOARD
            case 'admin.dashboard':
                $curr_page = 'Dashboard';
                break;
            
            // USER MANAGEMENT
            case 'admin.records':
            case 'admin.users':
            case 'admin.users.add':
            case 'admin.users.addMultiple':
            case 'admin.orgs':
                $curr_page = 'User Management';
                break;

            // START OF ADMIN CONTROLS 
            case 'admin.pendings':
            case 'admin.pendings.loadsearch': 
                $curr_page = 'Pending Requests';
                break; 


            // TEACHING LOADS
            case 'admin.loads.db': 
            case 'admin.loads': 
            case 'admin.loads.user': 
            case 'admin.loads.subj': 
            case 'admin.loads.add': 
            case 'admin.loads.store': 
            case 'admin.loads.search': 
            case 'admin.loads.user.search': 
            case 'admin.loads.batch': 
            case 'admin.loads.batch.subj': 
            case 'admin.loads.batch.users': 
            case 'admin.loads.upload':
                $curr_page = 'Teaching Loads'; 
                break;
            
            // SUBJECTS
            case 'admin.subjects': 
            case 'admin.subjects.search': 
            case 'admin.subjects.view': 
            case 'admin.subjects.add': 
            case 'admin.subjects.create': 
            case 'admin.subjects.delete.view':
                $curr_page = 'Subjects'; 
                break; 

            //PRC RESULTS
            case 'admin.prc':
            case 'admin.prc.add':
            case 'admin.prc.search':
            case 'admin.prc.view':
                $curr_page = 'PRC Results';
                break;

            // CERTIFICATES
            case 'admin.certs':
                $curr_page = 'Certificates';
                break; 

            // REGISTRY
            case 'admin.registry':
            case 'admin.registry.dept':
            case 'admin.sem':
                $curr_page = 'App Registry';
                break; 

                   // Dependencies
            case 'portal.dependencies.edit':
                $curr_page = 'Edit Dependent';
                break;
            case 'portal.dependencies.view':
                $curr_page = 'View Dependent';
                break;

            // Employment
            case 'portal.employment':
                $curr_page = 'Employment History';
                break;
            case 'portal.employment.edit':
                $curr_page = 'Edit Employment';
                break;
            case 'portal.employment.view':
                $curr_page = 'View Employment';
                break;

            // Research and Publications
            case 'portal.respub.success':
                $curr_page = 'Research/Publication Success';
                break;
            case 'portal.respub.edit_success':
                $curr_page = 'Edit Research/Publication Success';
                break;
            case 'portal.respub.view':
                $curr_page = 'View Research/Publications';
                break;
            case 'portal.respub.edit':
                $curr_page = 'Edit Research/Publications';
                break;

            // Teaching Loads
            case 'portal.loads':
                $curr_page = 'Teaching Loads';
                break;

            // Organization Memberships
            case 'portal.org':
                $curr_page = 'Organization Memberships';
                break;
            case 'portal.org.add':
                $curr_page = 'Add Organization Membership';
                break;
            case 'portal.org.edit':
                $curr_page = 'Edit Organization Membership';
                break;
            case 'portal.org.update':
                $curr_page = 'Update Organization Membership';
                break;
            case 'portal.org.store':
                $curr_page = 'Store Organization Membership';
                break;
            case 'portal.org.delete':
                $curr_page = 'Delete Organization Membership';
                break;
            case 'portal.org.clear':
                $curr_page = 'Clear Organization Memberships';
                break;

            // Licenses
            case 'portal.license':
                $curr_page = 'Licenses';
                break;
            case 'portal.license.view':
                $curr_page = 'View License';
                break;
            case 'portal.license.edit':
                $curr_page = 'Edit License';
                break;
            case 'portal.license.update':
                $curr_page = 'Update License';
                break;
            case 'portal.license.delete':
                $curr_page = 'Delete License';
                break;
            case 'portal.license.resubmit':
                $curr_page = 'Resubmit License';
                break;

            // Trainings
            case 'portal.training':
                $curr_page = 'Trainings';
                break;
            case 'portal.training.view':
                $curr_page = 'View Training';
                break;
            case 'portal.training.edit':
                $curr_page = 'Edit Training';
                break;
            case 'portal.training.update':
                $curr_page = 'Update Training';
                break;
            case 'portal.training.delete':
                $curr_page = 'Delete Training';
                break;
            case 'portal.training.resubmit':
                $curr_page = 'Resubmit Training';
                break;

            //Scholarships and Grants
            case 'scholarship-grants.dashboard':
                $curr_page = 'Scholarships and Grants';
                break;

            //SharePoint Sites
            case 'sharepoint-sites.dashboard':
                $curr_page = 'SharePoint Sites';
                break;

            //Information Hub Sites
            case 'information-hub.dashboard':
            case 'information-hub.add':
            case 'information-hub.edit-list':
            case 'information-hub.edit':
                $curr_page = 'Information Hub';
                break;

            // KPIs
            case 'kpis.dashboard':
            case 'kpis.show':
            case 'kpis.edit':
            case 'kpis.add':
                $curr_page = 'KPIs';
                break;

            // ISO Document Management
            case 'iso.document': 
            case 'iso.idc.dashboard': 
            case 'iso.management.index':
                $curr_page = 'ISO Document Handling';
                break;

            //Visit Counter
            case 'visitor-count.dashboard':
                $curr_page = 'Visitor Counter';
                break;
        
        }

        return $curr_page;
    }
}