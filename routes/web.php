<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CommonPagesController,
    Auth\LoginController,
    Auth\RegisterController,
    Auth\LogoutController,
    DashboardController,
    MyProfileController,
    Proposals\ProposalsController,
    GrantsController,
    UsersController,
    Proposals\CollaboratorsController,
    Proposals\PublicationsController,
    Proposals\ExpendituresController,
    Proposals\WorkplanController,
    Proposals\ResearchdesignController,
    ReportsController,
    Proposals\ProposalChangesController,
    BusinessMailingController,
    DepartmentsController,
    SchoolsController,
    Auth\CustomPasswordResetController,
    Auth\CustomVerificationController,
    FinancesController,
    ProjectsController,
    TestController,
    PDFController,
    SupervisionController,
    FinYearController,
};


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//test controller /endpoint
Route::get('/test', [TestController::class, 'test'])->name('api.test');

// Common Pages Routes
Route::get('/default', [CommonPagesController::class, 'index'])->name('pages.default');
Route::get('/about', [CommonPagesController::class, 'about'])->name('pages.about');
Route::get('/contact', [CommonPagesController::class, 'contact'])->name('pages.contact');
Route::get('/resetpassword', [CommonPagesController::class, 'resetpassword'])->name('pages.resetpassword');
Route::get('/setupadmin', [CommonPagesController::class, 'setupadmin'])->name('pages.setupadmin');
Route::post('/setupadmin', [CommonPagesController::class, 'makeInitialAdmin'])->name('api.makeinitialadmin');

//custom password reset
Route::get('password/reset', [CustomPasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [CustomPasswordResetController::class, 'sendResetLinkEmail'])->name('password.requestreset');
Route::get('password/reset/{token}', [CustomPasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [CustomPasswordResetController::class, 'reset'])->name('password.update');



// Authentication Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('pages.login');
Route::get('/index', [LoginController::class, 'showLoginForm'])->name('pages.login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('pages.login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('pages.register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
Route::match(['post', 'get'], '/logout', [LogoutController::class, 'logout'])->name('route.logout');
Route::get('/permission', [LoginController::class, 'subpermission'])->name('route.permission');

// Protected Routes
//custom account verification
Route::middleware('auth.custom')->group(function () {
    Route::get('email/verify', [CustomVerificationController::class, 'show'])->name('pages.account.verifyemail');
    Route::get('email/verify/{id}/{hash}', [CustomVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [CustomVerificationController::class, 'resend'])->name('verification.resend');
});

Route::middleware(['auth.custom', 'email.account.verification'])->group(function () {

    //Unauthorized
    Route::get('/unauthorized', [DashboardController::class, 'unauthorized'])->name('pages.unauthorized');

    //dashboard & home
    Route::get('/home', [DashboardController::class, 'home'])->name('pages.home');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('pages.dashboard');
    Route::get('/dashboard/chart', [DashboardController::class, 'chartdata'])->name('api.dashboard.chartdata');

    //proposals
    Route::get('/proposals/newproposal', [ProposalsController::class, 'getnewproposalpage'])->name('pages.proposals.viewnewproposal');
    Route::post('/proposals/post', [ProposalsController::class, 'postnewproposal'])->name('route.proposals.post');
    Route::post('/proposals/updatebasicdetails/{id}', [ProposalsController::class, 'updatebasicdetails'])->name('route.proposals.updatebasicdetails');
    Route::post('/proposals/updateresearch/{id}', [ProposalsController::class, 'updateresearchdetails'])->name('route.proposals.updateresearchdetails');
    Route::get('/proposals/allproposals', [ProposalsController::class, 'viewallproposals'])->name('pages.proposals.allproposals');
    Route::get('/proposals/myapplications', [ProposalsController::class, 'viewmyapplications'])->name('pages.proposals.myapplications');
    Route::get('/proposals/fetchmyapplications', [ProposalsController::class, 'fetchmyapplications'])->name('api.proposals.fetchmyapplications');
    Route::get('/proposals/fetchsearchproposals', [ProposalsController::class, 'fetchsearchproposals'])->name('api.proposals.fetchsearchproposals');
    Route::get('/proposals/fetchallproposals', [ProposalsController::class, 'fetchallproposals'])->name('api.proposals.fetchallproposals');
    Route::get('/proposals/collaborators/{id}', [ProposalsController::class, 'fetchcollaborators'])->name('api.proposals.fetchcollaborators');
    Route::get('/proposals/publications/{id}', [ProposalsController::class, 'fetchpublications'])->name('api.proposals.fetchpublications');
    Route::get('/proposals/expenditures/{id}', [ProposalsController::class, 'fetchexpenditures'])->name('api.proposals.fetchexpenditures');
    Route::get('/proposals/workplans/{id}', [ProposalsController::class, 'fetchworkplanitems'])->name('api.proposals.fetchworkplanitems');
    Route::get('/proposals/researchdesign/{id}', [ProposalsController::class, 'fetchresearchdesign'])->name('api.proposals.researchdesignitems');
    Route::get('/proposals/submissionstatus/{id}', [ProposalsController::class, 'fetchsubmissionstatus'])->name('api.proposals.submissionstatus');
    Route::get('/proposals/proposalchanges/{id}', [ProposalsController::class, 'fetchproposalchanges'])->name('api.proposals.proposalchanges');
    Route::post('/proposals/submit/{id}', [ProposalsController::class, 'submitproposal'])->name('api.proposals.submitproposal');
    Route::post('/proposals/receive/{id}', [ProposalsController::class, 'receiveproposal'])->name('api.proposals.receiveproposal');
    Route::post('/proposals/changeeditstatus/{id}', [ProposalsController::class, 'changeeditstatus'])->name('api.proposals.changeeditstatus');
    Route::post('/proposals/approvereject/{id}', [ProposalsController::class, 'approverejectproposal'])->name('api.proposals.approvereject');
    Route::get('/proposals/view/{id}', [ProposalsController::class, 'getsingleproposalpage'])->name('pages.proposals.viewproposal');
    Route::get('/proposals/edit/{id}', [ProposalsController::class, 'geteditsingleproposalpage'])->name('pages.proposals.editproposal');
    Route::get('/proposals/printpdf/{id}', [ProposalsController::class, 'printpdf'])->name('api.proposal.printpdf');
    //changes
    Route::post('/proposals/changes/post', [ProposalChangesController::class, 'postproposalchanges'])->name('api.proposalchanges.post');
    Route::get('/proposals/changes/fetchsearch', [ProposalChangesController::class, 'fetchsearch'])->name('api.proposalchanges.fetchsearch');
    Route::get('/proposals/changes/{id}/fetchall', [ProposalChangesController::class, 'fetchall'])->name('api.proposalchanges.fetchall');


    //schools
    Route::post('/schools/post', [SchoolsController::class, 'postnewschool'])->name('api.schools.post');
    Route::get('/schools/home', [SchoolsController::class, 'viewallschools'])->name('pages.schools.home');
    Route::get('/schools/fetchsearchschools', [SchoolsController::class, 'fetchsearchschools'])->name('api.schools.fetchsearchschools');
    Route::get('/schools/fetchallschools', [SchoolsController::class, 'fetchallschools'])->name('api.schools.fetchallschools');
    Route::get('/schools/view/{id}', [SchoolsController::class, 'getviewschoolpage'])->name('pages.schools.viewschool');
    Route::get('/schools/edit/{id}', [SchoolsController::class, 'geteditschoolpage'])->name('pages.schools.editschool');
    Route::post('/schools/edit/{id}', [SchoolsController::class, 'updateschool'])->name('api.schools.updateschool');

    //departments
    Route::post('/departments/post', [DepartmentsController::class, 'postnewdepartment'])->name('api.departments.post');
    Route::get('/departments/home', [DepartmentsController::class, 'viewalldepartments'])->name('pages.departments.home');
    Route::get('/departments/fetchsearchdepartments', [DepartmentsController::class, 'fetchsearchdepartments'])->name('api.departments.fetchsearchdepartments');
    Route::get('/departments/fetchalldepartments', [DepartmentsController::class, 'fetchalldepartments'])->name('api.departments.fetchalldepartments');
    Route::get('/departments/view/{id}', [DepartmentsController::class, 'getviewdepartmentpage'])->name('pages.departments.viewdepartment');
    Route::get('/departments/edit/{id}', [DepartmentsController::class, 'geteditdepartmentpage'])->name('pages.departments.editdepartment');
    Route::post('/departments/edit/{id}', [DepartmentsController::class, 'updatedepartment'])->name('api.departments.updatedepartment');


    //grants
    Route::post('/grants/post', [GrantsController::class, 'postnewgrant'])->name('api.grants.post');
    Route::get('/grants/home', [GrantsController::class, 'viewallgrants'])->name('pages.grants.home');
    Route::get('/grants/fetchsearchgrants', [GrantsController::class, 'fetchsearchgrants'])->name('api.grants.fetchsearchgrants');
    Route::get('/grants/fetchallgrants', [GrantsController::class, 'fetchallgrants'])->name('api.grants.fetchallgrants');
    Route::get('/grants/view/{id}', [GrantsController::class, 'getviewsinglegrantpage'])->name('pages.grants.viewgrant');
    Route::get('/grants/edit/{id}', [GrantsController::class, 'geteditsinglegrantpage'])->name('pages.grants.editgrant');
    Route::post('/grants/edit/{id}', [GrantsController::class, 'updategrant'])->name('api.grants.updategrant');
    //globalsettings
    Route::post('/grants/settings/finyear/post', [GrantsController::class, 'postcurrentfinyear'])->name('api.grants.settings.postcurrentfinyear');
    Route::post('/grants/settings/currentgrant/post', [GrantsController::class, 'postcurrentgrant'])->name('api.grants.settings.postcurrentgrant');



    //finyears
    Route::post('/finyear/post', [FinYearController::class, 'postnewfinyear'])->name('api.finyear.post');
    Route::get('/finyear/fetchallfinyears', [FinYearController::class, 'fetchallfinyears'])->name('api.finyear.fetchallfinyears');
    //  Route::get('/grants/fetchsearchgrants', [GrantsController::class, 'fetchsearchgrants'])->name('api.grants.fetchsearchgrants');
    //  Route::get('/grants/view/{id}', [GrantsController::class, 'getviewsinglegrantpage'])->name('pages.grants.viewgrant');
    //  Route::get('/grants/edit/{id}', [GrantsController::class, 'geteditsinglegrantpage'])->name('pages.grants.editgrant');
    //  Route::post('/grants/edit/{id}', [GrantsController::class, 'updategrant'])->name('api.grants.updategrant');

    //users
    Route::get('/users/manage', [UsersController::class, 'viewallusers'])->name('pages.users.manage');
    Route::get('/users/view/{id}', [UsersController::class, 'viewsingleuser'])->name('pages.users.viewsingleuser');
    Route::get('/users/fetchsearchusers', [UsersController::class, 'fetchsearchusers'])->name('api.users.fetchsearchusers');
    Route::get('/users/fetchallusers', [UsersController::class, 'fetchallusers'])->name('api.users.fetchallusers');
    Route::get('/notificationtype/fetchalltypes', [UsersController::class, 'fetchallnotificationtypes'])->name('api.users.fetchallnotificationtypes');
    Route::post('/users/updatebasicdetails/{id}', [UsersController::class, 'updatebasicdetails'])->name('api.users.updatebasicdetails');
    Route::post('/users/permissions/{id}', [UsersController::class, 'updateuserpermissions'])->name('api.users.updatepermissions');
    Route::post('/users/updaterole/{id}', [UsersController::class, 'updaterole'])->name('api.users.updaterole');
    Route::post('/users/resetpassword/{id}', [RegisterController::class, 'resetuserpassword'])->name('api.users.resetpassword');
    //notificationtypes
    Route::get('/notificationtype/view/{id}', [UsersController::class, 'managenotificationtype'])->name('pages.notificationtype.managenotificationtype');
    Route::post('/notificationtype/addusers/{id}', [UsersController::class, 'addnotifiableusers'])->name('api.notificationtype.addnotifiableusers');
    Route::post('/notificationtype/removeusers/{id}', [UsersController::class, 'removenotifiableuser'])->name('api.notificationtype.removenotifiableuser');
    Route::get('/notificationtype/fetchalltypes', [UsersController::class, 'fetchallnotificationtypes'])->name('api.notificationtype.fetchallnotificationtypes');
    Route::get('/notificationtype/fetchtypewiseusers/{id}', [UsersController::class, 'fetchtypewiseusers'])->name('api.notificationtype.fetchtypewiseusers');


    //collaborators
    Route::post('/collaborators/post', [CollaboratorsController::class, 'postcollaborator'])->name('api.collaborators.post');
    Route::get('/collaborators/fetchsearch', [CollaboratorsController::class, 'fetchsearch'])->name('api.collaborators.fetchsearch');
    Route::get('/collaborators/fetchall', [CollaboratorsController::class, 'fetchall'])->name('api.collaborators.fetchall');
    Route::get('/collaborators/edit/{id}', [CollaboratorsController::class, 'geteditsingleuserpage'])->name('pages.collaborators.editcollaborator');

    //publications
    Route::post('/publications/post', [PublicationsController::class, 'postpublication'])->name('api.publications.post');
    Route::get('/publications/fetchsearch', [PublicationsController::class, 'fetchsearch'])->name('api.publications.fetchsearch');
    Route::get('/publications/fetchall', [PublicationsController::class, 'fetchall'])->name('api.publications.fetchall');
    Route::get('/publications/edit/{id}', [PublicationsController::class, 'geteditsinglepublicationpage'])->name('pages.publications.editpublication');


    //expenditures
    Route::post('/expenditures/post', [ExpendituresController::class, 'postexpenditure'])->name('api.expenditures.post');
    Route::get('/expenditures/fetchsearch', [ExpendituresController::class, 'fetchsearch'])->name('api.expenditures.fetchsearch');
    Route::get('/expenditures/fetchall', [ExpendituresController::class, 'fetchall'])->name('api.expenditures.fetchall');
    Route::get('/expenditures/edit/{id}', [ExpendituresController::class, 'geteditsingleexpenditurepage'])->name('pages.expenditures.editexpenditures');

    //workplanitems
    Route::post('/workplan/post', [WorkplanController::class, 'postworkplanitem'])->name('api.workplan.post');
    Route::get('/workplan/fetchsearch', [WorkplanController::class, 'fetchsearch'])->name('api.expenditures.fetchsearch');
    Route::get('/workplan/fetchall', [WorkplanController::class, 'fetchall'])->name('api.expenditures.fetchall');
    Route::get('/workplan/edit/{id}', [WorkplanController::class, 'geteditsingleexpenditurepage'])->name('pages.expenditures.editexpenditures');


    //researchdesignitems
    Route::post('/researchdesign/post', [ResearchdesignController::class, 'postresearchdesignitem'])->name('api.researchdesign.post');
    Route::get('/researchdesign/fetchsearch', [ResearchdesignController::class, 'fetchsearch'])->name('api.researchdesign.fetchsearch');
    Route::get('/researchdesign/fetchall', [ResearchdesignController::class, 'fetchall'])->name('api.researchdesign.fetchall');
    Route::get('/researchdesign/edit/{id}', [ResearchdesignController::class, 'geteditsingleexpenditurepage'])->name('pages.researchdesign.editexpenditures');


    //projects
    Route::get('/projects/myprojects', [ProjectsController::class, 'myprojects'])->name('pages.projects.myprojects');
    Route::get('/projects/fetchmyactiveprojects', [ProjectsController::class, 'fetchmyactiveprojects'])->name('api.projects.fetchmyactiveprojects');
    Route::get('/projects/fetchmyallprojects', [ProjectsController::class, 'fetchmyallprojects'])->name('api.projects.fetchmyallprojects');
    Route::get('/projects/myprojects/{id}', [ProjectsController::class, 'viewmyproject'])->name('pages.projects.viewmyproject');
    Route::get('/projects/allprojects', [ProjectsController::class, 'allprojects'])->name('pages.projects.allprojects');
    Route::get('/projects/fetchallactiveprojects', [ProjectsController::class, 'fetchallactiveprojects'])->name('api.projects.fetchallactiveprojects');
    Route::get('/projects/fetchallprojects', [ProjectsController::class, 'fetchallprojects'])->name('api.projects.fetchallprojects');
    Route::get('/projects/fetchsearchallprojects', [ProjectsController::class, 'fetchsearchallprojects'])->name('api.projects.fetchsearchallprojects');
    Route::get('/projects/allprojects/{id}', [ProjectsController::class, 'viewanyproject'])->name('pages.projects.viewanyproject');
    //project progresss
    Route::post('/projects/submitmyprogress/{id}', [ProjectsController::class, 'submitmyprogress'])->name('api.projects.submitmyprogress');
    Route::get('/projects/fetchprojectprogress/{id}', [ProjectsController::class, 'fetchprojectprogress'])->name('api.projects.fetchprojectprogress');
    Route::post('/projects/pauseproject/{id}', [ProjectsController::class, 'pauseproject'])->name('api.projects.pauseproject');
    Route::post('/projects/resumeproject/{id}', [ProjectsController::class, 'resumeproject'])->name('api.projects.resumeproject');
    Route::post('/projects/cancelproject/{id}', [ProjectsController::class, 'cancelproject'])->name('api.projects.cancelproject');
    Route::post('/projects/completeproject/{id}', [ProjectsController::class, 'completeproject'])->name('api.projects.completeproject');
    Route::post('/projects/assignme/{id}', [ProjectsController::class, 'assignme'])->name('api.projects.assignme');

    //project funding
    Route::post('/projects/addfunding/{id}', [ProjectsController::class, 'addfunding'])->name('api.projects.addfunding');
    Route::get('/projects/fetchprojectfunding/{id}', [ProjectsController::class, 'fetchprojectfunding'])->name('api.projects.fetchprojectfunding');

    //supervision
    Route::get('/supervision/home', [SupervisionController::class, 'home'])->name('pages.supervision.home');
    Route::get('/supervision/monitoring/{id}', [SupervisionController::class, 'viewmonitoringpage'])->name('pages.supervision.monitoring.monitoringpage');
    Route::post('/supervision/monitoring/addreport/{id}', [SupervisionController::class, 'addreport'])->name('api.supervision.monitoring.addreport');
    Route::get('/supervision/monitoring/fetchmonitoringreport/{id}', [SupervisionController::class, 'fetchmonitoringreport'])->name('api.supervision.monitoring.fetchmonitoringreport');

    //profile
    Route::get('/myprofile', [MyProfileController::class, 'myprofile'])->name('pages.myprofile');

    //notifications
    Route::get('/mailing/home', [BusinessMailingController::class, 'mailinghome'])->name('pages.mailing.home');
    Route::get('/mailing/jobs/all', [BusinessMailingController::class, 'getalljobs'])->name('api.mailing.getalljobs');
    Route::get('/mailing/jobs/{id}', [BusinessMailingController::class, 'viewjobpage'])->name('pages.mailing.viewjobpage');
    Route::get('/mailing/failedjobs/all', [BusinessMailingController::class, 'getallfailedjobs'])->name('api.mailing.getallfailedjobs');
    Route::get('/mailing/failedjobs/{id}', [BusinessMailingController::class, 'viewfailedjobdetails'])->name('pages.mailing.viewfailedjobdetails');

    //reports
    Route::get('/reports/home', [ReportsController::class, 'home'])->name('pages.reports.home');
    Route::get('/api/reports/proposals/all', [ReportsController::class, 'getallproposals'])->name('api.reports.proposals.all');
    Route::get('/api/reports/proposals/byschool', [ReportsController::class, 'getproposalsbyschool'])->name('api.reports.proposals.byschool');
    Route::get('/api/reports/proposals/bytheme', [ReportsController::class, 'getproposalsbytheme'])->name('api.reports.proposals.bytheme');
    Route::get('/api/reports/proposals/bygrant', [ReportsController::class, 'getproposalsbygrant'])->name('api.reports.proposals.bygrant');
});