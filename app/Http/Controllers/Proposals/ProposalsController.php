<?php

namespace App\Http\Controllers\Proposals;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MailingController;
use App\Models\Collaborator;
use App\Models\Department;
use App\Models\Expenditureitem;
use App\Models\FinancialYear;
use App\Models\GlobalSetting;
use App\Models\Grant;
use App\Models\Permission;
use App\Models\Proposal;
use App\Models\Publication;
use App\Models\ResearchDesignItem;
use App\Models\ResearchProject;
use App\Models\ResearchTheme;
use App\Models\Workplan;
use App\Models\User;
use App\Models\ProposalChanges;
use App\Notifications\ProposalSubmitted;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteUri;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Notification;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;



class ProposalsController extends Controller
{
    //
    public function getnewproposalpage()
    {
        if (!auth()->user()->haspermission('canmakenewproposal')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Make a new Proposal!");
        }
        $isnewprop = true;
        $departments = Department::all();
        $themes = ResearchTheme::all();
        $user = auth()->user();
        $currentgrant = GlobalSetting::where('item', 'current_open_grant')->first();
        $grants = null;
        if ($currentgrant) {
            $grants = Grant::where('grantid', $currentgrant->value1)
                ->whereDoesntHave('proposals', function ($query) use ($user) {
                    $query->where('useridfk', $user->userid);
                })->get();
        }

        return view('pages.proposals.proposalform', compact('isnewprop', 'departments', 'grants', 'themes'));
    }

    public function postnewproposal(Request $request)
    {
        if (!auth()->user()->haspermission('canmakenewproposal')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Make a new Proposal!");
        }
        // Define validation rules
        $rules = [
            'grantnofk' => 'required|integer',
            'departmentfk' => 'required|string',
            'pfnofk' => 'required|integer',
            'themefk' => 'required|string',
            'highestqualification' => 'required|string',
            'officephone' => 'required|string',
            'cellphone' => 'required|string',
            'faxnumber' => 'required|string',
        ];

        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the grant has already been applied for
        if ($this->isgrantapplied($request->input('grantnofk'))) {
            // Proposal exists for the current user and grant number
            return redirect()->route('pages.proposals.viewnewproposal')
                ->with('success', 'Proposal exists for grant number');
        }
        $grant = Grant::findOrFail($request->input('grantnofk'));
        // Generate proposal code 
        $currentYear = date('Y');
        $currentMonth = date('Y');
        $lastRecord = Proposal::orderBy('proposalid', 'desc')->first();
        $incrementNumber = $lastRecord ? $lastRecord->proposalid + 1 : 1;
        $generatedCode = 'UOK/ARG/P/' . $currentYear . '/' . $currentMonth . '/' . $incrementNumber;

        // Create a new proposal instance
        $proposal = new Proposal();

        // Assign values from the request
        $proposal->proposalid = $incrementNumber;
        $proposal->proposalcode = $generatedCode;
        $proposal->grantnofk = $request->input('grantnofk');
        $proposal->departmentidfk = $request->input('departmentfk');
        $proposal->useridfk = Auth::user()->userid;
        $proposal->pfnofk = $request->input('pfnofk');
        $proposal->approvalstatus = 'Pending';
        $proposal->highqualification = $request->input('highestqualification');
        $proposal->officephone = $request->input('officephone');
        $proposal->cellphone = $request->input('cellphone');
        $proposal->faxnumber = $request->input('faxnumber');
        $proposal->themefk = $request->input('themefk');

        // Save the proposal
        $proposal->save();
        // Redirect to the edit proposal page with a success message
        return redirect()->route('pages.proposals.editproposal', ['id' => $proposal->proposalid, 'has_message' => 'Basic Details Successfully Saved!'])
            ->with('success', 'Basic Details Saved Successfully!!');
    }

    private function isgrantapplied($grantno)
    {
        try {
            // Get the current user's ID (assuming you have authenticated users)
            $userid = auth()->id();

            // Check if a proposal exists with the given $grantno and for the current user
            $proposalExists = Proposal::where('grantnofk', $grantno)
                ->where('useridfk', $userid)
                ->exists();

            return $proposalExists;
        } catch (Exception $e) {
            // Handle any exceptions, such as database errors
            return true; // Return true to indicate an error occurred (adjust as needed)
        }
    }

    public function updatebasicdetails(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        if (!auth()->user()->userid == $proposal->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to edit this Proposal. Only the owner can Edit!");
        }
        $rules = [
            'grantnofk' => 'required|integer', // Example rules, adjust as needed
            'departmentfk' => 'required|string',
            'themefk' => 'required|string',
            'highestqualification' => 'required|string',
            'officephone' => 'required|string',
            'cellphone' => 'required|string',
            'faxnumber' => 'required|string',
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        // Assign values from the request
        $proposal->departmentidfk = $request->input('departmentfk');
        $proposal->grantnofk = $request->input('grantnofk');
        $proposal->themefk = $request->input('themefk');
        $proposal->highqualification = $request->input('highestqualification'); // Example qualification
        $proposal->officephone = $request->input('officephone');
        $proposal->cellphone = $request->input('cellphone');
        $proposal->faxnumber = $request->input('faxnumber');
        // Save the proposal
        $proposal->save();

        // Optionally, return a response or redirect
        // return response()->json(['message' => 'Proposal created successfully'], 201);
        // return response()->json('success', 'Basic Details Saved Successfully!!');
        return redirect()->route('pages.proposals.editproposal', ['id' => $proposal->proposalid, 'has_message' => true])->with('success', 'Basic Details Saved Successfully!!');


    }

    public function updateresearchdetails(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        if (!auth()->user()->userid == $proposal->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to edit this Proposal. Only the owner can Edit!");
        }
        $rules = [
            'researchtitle' => 'required|string', // Example rules, adjust as needed
            'objectives' => 'required|string',
            'hypothesis' => 'required|string',
            'significance' => 'required|string',
            'ethicals' => 'required|string',
            'outputs' => 'required|string',
            'economicimpact' => 'required|string',
            'res_findings' => 'required|string',
            'terminationdate' => 'required|date',
            'commencingdate' => 'required|date',
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        // Assign values from the request
        $proposal->researchtitle = $request->input('researchtitle');
        $proposal->objectives = $request->input('objectives');
        $proposal->hypothesis = $request->input('hypothesis');
        $proposal->significance = $request->input('significance'); // Example qualification
        $proposal->ethicals = $request->input('ethicals');
        $proposal->expoutput = $request->input('outputs');
        $proposal->socio_impact = $request->input('economicimpact');
        $proposal->res_findings = $request->input('res_findings');
        $proposal->commencingdate = $request->input('commencingdate');
        $proposal->terminationdate = $request->input('terminationdate');
        // Save the proposal
        $proposal->save();

        return redirect()->route('pages.proposals.editproposal', ['id' => $proposal->proposalid])->with('success', 'Research Details Saved Successfully!!');


    }

    public function submitproposal(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        if (!auth()->user()->userid == $proposal->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Submit this Proposal. Only the owner can Submit!");
        }
        if ($proposal->submittedstatus) {
            return response(['message' => 'Application has already been submitted!', 'type' => 'danger']);
        }
        if ($proposal->receivedstatus) {
            return response(['message' => 'This Proposal has been received before!!', 'type' => 'danger']);
        }
        $cansubmit = $this->cansubmit($id);
        if (isset($cansubmit)) {
            $proposal->submittedstatus = true;
            $proposal->caneditstatus = false;
            $proposal->save();
            //notifiable users to be informed of new proposal
            $mailingController = new MailingController();
            $url = route('pages.proposals.viewproposal', ['id' => $proposal->proposalid]);
            $mailingController->notifyUsersOfProposalActivity('proposalsubmitted', 'New Proposal', 'success', ['You have a New Proposal Pending Receival and processing.'], 'View Proposal', $url);

            return response(['message' => 'Application Submitted Successfully!!', 'type' => 'success']);
        } else {
            return response(['message' => 'Application not ready for Submission. Has incomplete Details!', 'type' => 'warning']);
        }

    }
    public function receiveproposal(Request $request, $id)
    {
        if (!auth()->user()->haspermission('canreceiveproposal')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to receive this Proposal!");
        }

        $proposal = Proposal::findOrFail($id);
        if (!$proposal->submittedstatus) {
            return response(['message' => 'This proposal has not been submitted!', 'type' => 'warning']);
        }
        if ($proposal->receivedstatus) {
            return response(['message' => 'This Proposal has been received before!!', 'type' => 'danger']);
        }
        $proposal->receivedstatus = true;
        $proposal->caneditstatus = false;
        $proposal->save();
        $mailingController = new MailingController();
        $Url = route('pages.proposals.viewproposal', ['id' => $proposal->proposalid]);
        $mailingController->notifyUsersOfProposalActivity('proposalreceived', 'Proposal Received!', 'success', ['Your Proposal has been Received Successfully.'], 'View Proposal', $Url);
        return response(['message' => 'Proposal received Successfully!!', 'type' => 'success']);


    }
    public function changeeditstatus(Request $request, $id)
    {
        if (!auth()->user()->haspermission('canenabledisableproposaledit')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Enable or Disable editing of this Proposal!");
        }

        $proposal = Proposal::findOrFail($id);

        $proposal->caneditstatus = false;
        $proposal->save();
        $mailingController = new MailingController();
        $mailingController->notifyUserReceivedProposal($proposal);
        return response(['message' => 'Proposal received Successfully!!', 'type' => 'success']);


    }
    public function approverejectproposal(Request $request, $id)
    {
        if ($request->input('status') == "Approved" && auth()->user()->haspermission('canapproveproposal')) {
        } else if ($request->input('status') == "Rejected" && auth()->user()->haspermission('canrejectproposal')) {
        } else {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to  Approve/Reject this Proposal!");
        }

        $rules = [
            'comment' => 'required|string',
            'status' => 'required|string',
            'fundingfinyearfk' => [
                'required_if:status,Approved',
                'nullable',
                'string',
            ],
        ];
        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => "Please provide a comment,Funding Year & status!", 'type' => "warning"], 400);
        }

        $proposal = Proposal::findOrFail($id);
        if (!$proposal->submittedstatus) {
            return response(['message' => 'This Proposal has not been Submitted by the owner!!', 'type' => 'danger']);
        }
        if (!$proposal->receivedstatus) {
            return response(['message' => 'This Proposal has not been Received!!', 'type' => 'danger']);
        }
        if ($proposal->approvalstatus == 'Rejected' || ResearchProject::where('proposalidfk', $id)->exists()) {
            return response(['message' => 'This Proposal has been Approved or Rejected before!!', 'type' => 'danger']);
        }
        DB::transaction(function () use ($id, $request) {
            $proposal = Proposal::findOrFail($id);
            $proposal->approvalstatus = $request->input('status');
            $proposal->comment = $request->input('comment');
            $proposal->caneditstatus = false;
            $proposal->saveOrFail();

            $yearid = GlobalSetting::where('item', 'current_fin_year')->first();
            $currentyear = FinancialYear::findOrFail($yearid->value1);

            if ($request->input('status') == "Approved") {
                $lastRecord = ResearchProject::orderBy('researchid', 'desc')->first();
                $incrementNumber = $lastRecord ? $lastRecord->researchid + 1 : 1;
                $generatedCode = 'UOK/ARG/' . $currentyear->finyear . '/' . $incrementNumber;
                // new project
                $project = new ResearchProject();
                $project->researchnumber = $generatedCode;
                $project->proposalidfk = $proposal->proposalid;
                $project->projectstatus = 'Active';
                $project->ispaused = false;
                $project->fundingfinyearfk = $request->input('fundingfinyearfk');
                $project->saveOrFail();
            }

        });
        if ($request->input('status') == "Approved") {
            $project = ResearchProject::where('proposalidfk', $id)->firstOrFail();
            $mailingController = new MailingController();
            $url = route('pages.projects.viewanyproject', ['id' => $project->researchid]);
            $mailingController->notifyUsersOfProposalActivity('proposalapproved', 'Proposal Approved!', 'success', ['This Proposal has been Approved Successfully.', 'The project will kick off on the indicated Start Date.'], 'View Project', $url);
            return response(['message' => 'Proposal Approved Successfully! Project Started!', 'type' => 'success']);
        } else if ($request->input('status') == "Rejected") {
            $mailingController = new MailingController();
            $url = route('pages.proposals.viewproposal', ['id' => $id]);
            $mailingController->notifyUsersOfProposalActivity('proposalrejected', 'Proposal Rejected', 'success', ['The project didnt qualify for further steps.'], 'View Proposal', $url);
            return response(['message' => 'Proposal Rejected Successfully!!', 'type' => 'danger']);
        } else {
            return response(['message' => 'Unknown Action on Status!!', 'type' => 'danger']);
        }



    }
    public function cansubmit($id)
    {
        $response = $this->querysubmissionstatus($id);
        if ($response['basic'] == 2 && $response['design'] == 2 && $response['expenditure'] == 2 && $response['workplan'] == 2 && $response['researchinfo'] == 2) {
            return true;
        } else {
            return false;
        }

    }
    public function canapprove($id)
    {
        $user = User::findOrFail($id);

        if ($user->haspermission($id)) {
            return true;
        } else {
            return false;
        }

    }
    public function viewallproposals()
    {
        if (!auth()->user()->haspermission('canviewallapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view all Proposals!");
        }
        $allproposals = Proposal::all();
        return view('pages.proposals.allproposals', compact('allproposals'));
    }
    public function viewmyapplications()
    {
        if (!auth()->user()->haspermission('canviewmyapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view My Proposals!");
        }
        $userid = auth()->user()->userid;
        $allproposals = Proposal::where('useridfk', $userid);
        return view('pages.proposals.myapplications', compact('allproposals'));
    }
    public function getsingleproposalpage($id)
    {
        $user = Auth::user();
        // Find the proposal by ID or fail with a 404 error
        $prop = Proposal::findOrFail($id);

        if (!$user->haspermission('canreadproposaldetails') && $user->userid != $prop->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to read the requested Proposal!");
        }
        $grants = Grant::all();
        $departments = Department::all();
        $themes = ResearchTheme::all();
        $finyears = FinancialYear::all();
        return view('pages.proposals.readproposalform', compact('prop', 'departments', 'grants', 'themes', 'finyears'));
    }
    public function printpdf($id)
    {
        $proposal = Proposal::with(['applicant', 'department', 'themeitem',])->findOrFail($id);
        // Load your Blade view here
        $pdf = Pdf::loadView('pages.proposals.printproposal', compact('proposal'));

        // Optionally, you can set the paper size and orientation
        $pdf->setPaper('A4', 'potrait');
        // Return the generated PDF 
        // return $pdf->download('Application-' . str_replace('/', '-', $proposal->proposalcode) . '.pdf');
        return $pdf->stream();
    }
    public function geteditsingleproposalpage(Request $req, $id)
    {
        $prop = Proposal::findOrFail($id);
        if (!auth()->user()->userid == $prop->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Edit the requested Proposal!");
        }
        if (!$prop->caneditstatus || $prop->approvalstatus != 'Pending') {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "The Proposal is not Editable!");
        }
        $grants = Grant::all();
        $departments = Department::all();
        $themes = ResearchTheme::all();
        $hasmessage = ($req->input('has_message', 0) == 1) ? true : false;
        // Return the view with the proposal data
        return view('pages.proposals.proposalform', compact('prop', 'departments', 'grants', 'themes', 'hasmessage'));

    }

    public function fetchmyapplications()
    {
        if (!auth()->user()->haspermission('canviewmyapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view My Proposals!");
        }
        $user = auth()->user();
        $myapplications = Proposal::where('useridfk', $user->userid)->with('department', 'grantitem', 'themeitem', 'applicant')->get();
        $proposals = $myapplications->map(function ($proposal) use ($user) {
            $proposal->haspendingupdates = $proposal->hasPendingUpdates();
            return $proposal;
        });
        return response()->json($proposals);
    }

    public function fetchallproposals()
    {
        if (!auth()->user()->haspermission('canviewallapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view all Proposals!");
        }
        $data = Proposal::with('department', 'grantitem', 'themeitem', 'applicant')->get();
        return response()->json($data); // Return  data as JSON
    }

    public function fetchsearchproposals(Request $request)
    {
        if (!auth()->user()->haspermission('canviewallapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view all Proposals!");
        }
        $searchTerm = $request->input('search');
        $data = Proposal::with('department', 'grantitem', 'themeitem', 'applicant')
            ->where('approvalstatus', 'like', '%' . $searchTerm . '%')
            ->orWhere('highqualification', 'like', '%' . $searchTerm . '%')
            ->orWhereHas('themeitem', function ($query) use ($searchTerm) {
                $query->where('themename', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('applicant', function ($query1) use ($searchTerm) {
                $query1->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('department', function ($query) use ($searchTerm) {
                $query->where('shortname', 'like', '%' . $searchTerm . '%');
            })
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }

    public function fetchcollaborators($id)
    {
        $data = Collaborator::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }

    public function fetchpublications($id)
    {
        $data = Publication::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }

    public function fetchexpenditures($id)
    {
        $data = Expenditureitem::where('proposalidfk', $id)
            ->get();
        $summary = [];
        $totalOthers = $data->where('itemtype', 'Others')
            ->sum('total');

        $totalTravels = $data->where('itemtype', 'Travels')
            ->sum('total');

        $totalConsumables = $data->where('itemtype', 'Consumables')
            ->sum('total');

        $totalFacilities = $data->where('itemtype', 'Facilities')
            ->sum('total');
        $summary['totalOthers'] = $totalOthers;
        $summary['totalTravels'] = $totalTravels;
        $summary['totalConsumables'] = $totalConsumables;
        $summary['totalFacilities'] = $totalFacilities;
        $summary['totalExpenditures'] = $totalFacilities + $totalConsumables + $totalTravels + $totalOthers;
        $rule_40 = $totalOthers + $totalTravels;
        $rule_60 = $totalFacilities + $totalConsumables;
        $summary['isValidBudget'] = $this->getIsValidBudget($rule_40, $rule_60);

        return response(compact(['data', 'summary'])); // Return filtered data as JSON
    }
    private function getIsValidBudget($rule_40, $rule_60)
    {
        $total = $rule_40 + $rule_60;
        if ($rule_40 <= (0.4 * $total)) {
            return true;
        } else {
            return false;
        }
    }
    public function fetchworkplanitems($id)
    {
        $data = Workplan::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }
    public function fetchresearchdesign($id)
    {
        $data = ResearchDesignItem::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }
    public function querysubmissionstatus($id)
    {
        //0 -not required
        //1 -not completed
        //2 -completed
        $prop = Proposal::findOrFail($id);
        $researchinfo = 1;
        if ($prop->researchtitle && $prop->objectives && $prop->commencingdate && $prop->terminationdate && $prop->hypothesis && $prop->significance && $prop->ethicals && $prop->expoutput && $prop->socio_impact && $prop->res_findings) {
            $researchinfo = 2;
        }
        $basic = ($prop) ? 2 : 1;
        $design = (ResearchDesignItem::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        $finanncials = (Expenditureitem::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        $workplan = (Workplan::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        $collaborators = (Collaborator::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        ;
        $publications = (Publication::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        ;
        $appstatus = array('basic' => $basic, 'researchinfo' => $researchinfo, 'design' => $design, 'workplan' => $workplan, 'collaborators' => $collaborators, 'publications' => $publications, 'expenditure' => $finanncials);
        return $appstatus;
    }

    public function fetchsubmissionstatus($id)
    {
        $data = $this->querysubmissionstatus($id);
        $cansubmit = $this->cansubmit($id);
        return response(['data' => $data, 'cansubmitstatus' => $cansubmit]);
    }

    public function fetchproposalchanges($id)
    {
        $data = ProposalChanges::where('proposalidfk', $id)->with('suggestedby')->get();
        return response()->json($data);
    }
}

