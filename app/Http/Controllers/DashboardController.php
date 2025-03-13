<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ResearchFunding;
use App\Models\ResearchProject;
use App\Models\ResearchTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function home()
    {
        // Get the current user's ID
        $userid = auth()->user()->userid;
        $proposals = Proposal::where('useridfk', $userid)->get();
        $proposalsids = $proposals->pluck('proposalid');
        $projects = ResearchProject::whereIn('proposalidfk', $proposalsids)->get();
        $projectsids = $projects->pluck('researchid');
        $fundings = ResearchFunding::whereIn('researchidfk', $projectsids)->get();

        // Get  proposals count
        $totalProposals = $proposals->count();
        $approvedProposals = $proposals->where('approvalstatus', 'approved')->count();
        $rejectedProposals = $proposals->where('approvalstatus', 'rejected')->count();
        $pendingProposals = $proposals->where('approvalstatus', 'pending')->count();
        //get projects counts
        $activeprojects = $projects->where('projectstatus', 'Active')->count();
        $cancelledprojects = $projects->where('projectstatus', 'Cancelled')->count();
        $completedprojects = $projects->where('projectstatus', 'Completed')->count();
        //total funds
        $totalAmountReceived = $fundings->sum('amount');
        $dashboardmetrics = [
            'totalProposals' => $totalProposals,
            'approvedProposals' => $approvedProposals,
            'pendingProposals' => $pendingProposals,
            'totalAmountReceived' => $totalAmountReceived,
            'rejectedProposals' => $rejectedProposals,
            'activeprojects' => $activeprojects,
            'cancelledprojects' => $cancelledprojects,
            'completedprojects' => $completedprojects,
        ];

        return view('pages.home', $dashboardmetrics);
    }

    public function dashboard()
    {
        if (!auth()->user()->haspermission('canviewadmindashboard')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to View Admin Dashboard!");
        }


        // Get  proposals count
        $allProposalscount = Proposal::count();
        $approvedProposalsCount = Proposal::where('approvalstatus', 'approved')->count();
        $rejectedProposalsCount = Proposal::where('approvalstatus', 'rejected')->count();
        $pendingProposalsCount = Proposal::where('approvalstatus', 'pending')->count();
        // $requirechangeProposalsCount = Proposal::where('approvalstatus', 'requirechange')->count();
        $dashboardmetrics = [
            'allProposalscount' => $allProposalscount,
            'approvedProposalsCount' => $approvedProposalsCount,
            'pendingProposalsCount' => $pendingProposalsCount,
            // 'requirechangeProposalsCount' => $requirechangeProposalsCount,
            'rejectedProposalsCount' => $rejectedProposalsCount,
        ];

        return view('pages.dashboard', $dashboardmetrics);
    }
    public function chartdata1()
    {
        if (!auth()->user()->haspermission('canviewadmindashboard')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to View Admin Dashboard!");
        }
        $themes = ResearchTheme::all();
        // Count proposals grouped by theme
        $themeCounts = Proposal::join('researchthemes', 'proposals.themefk', '=', 'researchthemes.themeid')
            ->select('researchthemes.themename as themename', DB::raw('count(proposals.proposalid) as total'))
            ->groupBy('themename')
            ->get()
            ->toArray();

        // Prepare the data array
        $data = [
            ['Research Theme', 'Applications Count'], // Header row
        ];

        foreach ($themes as $theme) {
            $count = 0;
            foreach ($themeCounts as $tc) {
                if ($tc['themename'] === $theme->themename) {
                    $count = $tc['total'];
                    break;
                }
            }
            $data[] = [
                $theme->themename,
                $count,
            ];
        }
        // Get  proposals count
        $allProposalscount = Proposal::count();
        $approvedProposalsCount = Proposal::where('approvalstatus', 'approved')->count();
        $rejectedProposalsCount = Proposal::where('approvalstatus', 'rejected')->count();
        $pendingProposalsCount = Proposal::where('approvalstatus', 'pending')->count();
        $requirechangeProposalsCount = Proposal::where('approvalstatus', 'requirechange')->count();
        $dashboardmetrics = [
            'allProposalscount' => $allProposalscount,
            'approvedProposalsCount' => $approvedProposalsCount,
            'pendingProposalsCount' => $pendingProposalsCount,
            'requirechangeProposalsCount' => $requirechangeProposalsCount,
            'rejectedProposalsCount' => $rejectedProposalsCount,
            'themeCounts' => $data
        ];

        return view('pages.dashboard', $dashboardmetrics);
    }
    public function chartdata(Request $request)
    {
        if (!auth()->user()->haspermission('canviewadmindashboard')) {
            return [];
        }

        $proposalsQuery = Proposal::with('department', 'grantitem', 'themeitem', 'applicant')->where('submittedstatus', true);


        $themes = ResearchTheme::all();
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Total Proposals',
                    'backgroundColor' => 'rgba(17, 126, 73, 1)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'data' => []
                ],
                [
                    'label' => 'Male Applicants',
                    'backgroundColor' => 'rgba(236, 141, 87, 1)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'data' => []
                ],
                [
                    'label' => 'Female Applicants',
                    'backgroundColor' => 'rgba(236, 87, 182, 1)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => []
                ],
                [
                    'label' => 'Approved Proposals',
                    'backgroundColor' => 'rgba(87, 148, 236, 1)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'data' => []
                ],
                [
                    'label' => 'Rejected Proposals',
                    'backgroundColor' => 'rgba(207, 210, 101, 1)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => []
                ],
                [
                    'label' => 'Pending Proposals',
                    'backgroundColor' => 'rgba(101, 173, 45, 0.47)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => []
                ]
            ]
        ];

        foreach ($themes as $theme) {
            $filteredProposals = (clone $proposalsQuery)->where('themefk', $theme->themeid)->get();

            $approvedCount = $filteredProposals->where('approvalstatus', 'Approved')->count();
            $rejectedCount = $filteredProposals->where('approvalstatus', 'Rejected')->count();
            $pendingCount = $filteredProposals->where('approvalstatus', 'Pending')->count();

            $maleCount = $filteredProposals->filter(fn($proposal) => $proposal->applicant->gender == 'Male')->count();
            $femaleCount = $filteredProposals->filter(fn($proposal) => $proposal->applicant->gender == 'Female')->count();

            $chartData['labels'][] = $theme->themename;

            $chartData['datasets'][0]['data'][] = $filteredProposals->count();
            $chartData['datasets'][1]['data'][] = $maleCount;
            $chartData['datasets'][2]['data'][] = $femaleCount;
            $chartData['datasets'][3]['data'][] = $approvedCount;
            $chartData['datasets'][4]['data'][] = $rejectedCount;
            $chartData['datasets'][5]['data'][] = $pendingCount;
        }

        return response()->json($chartData);
    }

    public function unauthorized()
    {
        $message = session('unauthorizationmessage');

        return view('pages.unauthorized', ['message' => $message]);

    }

}
