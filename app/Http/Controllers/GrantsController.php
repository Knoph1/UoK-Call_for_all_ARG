<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use App\Models\GlobalSetting;
use App\Models\Grant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GrantsController extends Controller
{
    //
    public function postnewgrant(Request $request)
    {
        if (!auth()->user()->haspermission('canaddoreditgrant')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Add or Edit a Grant!");
        }
        // Validate incoming request data if needed
        // Define validation rules
        $rules = [
            'title' => 'required|string', // Example rules, adjust as needed
            'finyearfk' => 'required|string', // Adjust data types as per your schema
            'status' => 'required|string',
        ];




        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors()], 400);
            return response(['message' => 'Fill all the required Fields!', 'type' => 'danger'], 400);

        }

        // Assuming you're retrieving grantno, departmentid, and userid from the request
        $grant = new Grant(); // Ensure the model name matches your actual model class name
        // Assign values from the request
        $grant->title = $request->input('title');
        $grant->finyearfk = $request->input('finyearfk');
        $grant->status = $request->input('status');
        $grant->save();

        // Optionally, return a response or redirect
        // return response()->json(['message' => 'Proposal created successfully'], 201);
        return response(['message' => 'Grant Saved Successfully!!', 'type' => 'success']);


    }

    public function updategrant(Request $request, $id)
    {
        if (!auth()->user()->haspermission('canaddoreditgrant')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Add or Edit a Grant!");
        }
        // Validate incoming request data if needed
        // Define validation rules
        $rules = [
            'title' => 'required|string', // Example rules, adjust as needed
            'finyear' => 'required|string', // Adjust data types as per your schema
            'status' => 'required|string',
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors()], 400);
            return response(['message' => 'Fill all the required Fields!', 'type' => 'danger'], 400);

        }

        // Assuming you're retrieving grantno, departmentid, and userid from the request
        $grant = Grant::findOrFail($id); // Ensure the model name matches your actual model class name
        // Assign values from the request
        $grant->title = $request->input('title');
        $grant->finyear = $request->input('finyear');
        $grant->status = $request->input('status');
        $grant->save();

        // Optionally, return a response or redirect
        // return response()->json(['message' => 'Proposal created successfully'], 201);
        return response(['message' => 'Grant Updated Successfully!!', 'type' => 'success']);


    }
    public function viewallgrants()
    {
        $allgrants = Grant::with('financialyear')->get();
        $finyears = FinancialYear::all();
        $currentsettings =[
            'current_grant'=>GlobalSetting::where('item','current_open_grant')->first()->value1 ?? null,
            'current_year'=>GlobalSetting::where('item','current_fin_year')->first()->value1 ?? null
        ];
        return view('pages.grants.home', compact('allgrants', 'finyears','currentsettings'));
    }
    public function getviewsinglegrantpage($id)
    {
        if (!auth()->user()->haspermission('canaddoreditgrant')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Add or Edit a Grant!");
        }
        // Find the grant by ID or fail with a 404 error
        $grant = Grant::findOrFail($id);
        $isreadonlypage = true;
        $isadminmode = true;
        // Return the view with the grant data
        return view('pages.grants.grantform', compact('grant', 'isreadonlypage', 'isadminmode'));
    }
    public function geteditsinglegrantpage($id)
    {
        if (!auth()->user()->haspermission('canaddoreditgrant')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Add or Edit a Grant!");
        }
        // Find the grant by ID or fail with a 404 error
        $grant = Grant::findOrFail($id);
        $isreadonlypage = true;
        $isadminmode = true;
        // Return the view with the grant data
        return view('pages.proposals.proposalform', compact('isreadonlypage', 'isadminmode', 'grant'));
    }

    public function fetchallgrants()
    {
        $data = Grant::with('financialyear')->get();
        return response()->json($data); // Return  data as JSON
    }

    public function fetchsearchgrants(Request $request)
    {
        $searchTerm = $request->input('search');
        $data = Grant::all()->where('finyear', 'like', '%' . $searchTerm . '%')
            ->orWhere('grantid', 'like', '%' . $searchTerm . '%')
            ->orWhere('status', 'like', '%' . $searchTerm . '%')
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }


    //settings
    public function postcurrentgrant(Request $request)
    {
        if (!auth()->user()->haspermission('canupdatecurrentgrantandyear')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Update Current Settings!");
        }
        // Validate incoming request data if needed
        // Define validation rules
        $rules = [ 
            'current_grantno' => 'required|string',
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors()], 400);
            return response(['message' => 'Fill all the required Fields!', 'type' => 'danger'], 400);

        }

        $item = GlobalSetting::where('item','current_open_grant')->firstOrFail(); 
        $item->value1 = $request->input('current_grantno'); 
        $item->save();

        // Optionally, return a response or redirect
        // return response()->json(['message' => 'Proposal created successfully'], 201);
        return response(['message' => 'Current Grant Updated Successfully!!', 'type' => 'success']);


    }

    public function postcurrentfinyear(Request $request)
    {
        if (!auth()->user()->haspermission('canupdatecurrentgrantandyear')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Update Current Settings!");
        }
        // Validate incoming request data if needed
        // Define validation rules
        $rules = [ 
            'current_fin_year' => 'required|string',
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors()], 400);
            return response(['message' => 'Fill all the required Fields!', 'type' => 'danger'], 400);

        }

        $item = GlobalSetting::where('item','current_fin_year')->firstOrFail(); 
        $item->value1 = $request->input('current_fin_year'); 
        $item->save();

        // Optionally, return a response or redirect
        // return response()->json(['message' => 'Proposal created successfully'], 201);
        return response(['message' => 'Current Financial Year Updated Successfully!!', 'type' => 'success']);


    }
}
