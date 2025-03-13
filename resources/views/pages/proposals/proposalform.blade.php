@extends('layouts.master')

@section('content')

@if (isset($grants))
    <div>
        @if (isset($hasmessage) && $hasmessage)
            <script>
                $(document).ready(function () {
                    showtoastmessage({ 'message': "{{$hasmessage}}" });
                });
            </script>
        @endif

        <style>
            .prop-tabcontainer {
                background-color: #FAF9F6;
                border-radius: 4px;
            }

            .prop-tabpanel {
                border-width: 1px;
                border-color: gray;
                background-color: #FAF9F6;
                border-style: solid;
                border-radius: 4px;
                padding: 8px;
            }

            .form-group {
                margin-bottom: 6px;
            }
        </style>
        @if (isset($isnewprop) && isset($grants) && $grants->count()<=0)
        <div class="alert alert-primary text-center mt-5"><b>No Call for Grants to Apply For!!</b></div>
        @else
        <div class="prop-tabcontainer">
            <!-- Nav tabs -->
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">

                    <button class="nav-link active" id="nav-personal-tab" data-bs-toggle="tab"
                        data-bs-target="#panel-basicdetails" type="button" role="tab" aria-controls="panel-basicdetails"
                        aria-selected="true">Basic Details</button>
                    <button class="nav-link" id="nav-research-tab" data-bs-toggle="tab" data-bs-target="#panel-research"
                        type="button" role="tab" aria-controls="panel-profile" aria-selected="false">Research</button>
                    <button class="nav-link" id="nav-collaboration-tab" data-bs-toggle="tab"
                        data-bs-target="#panel-collaboration" type="button" role="tab" aria-controls="panel-collaboration"
                        aria-selected="false">C & P Lists</button>
                    <button class="nav-link" id="nav-finance-tab" data-bs-toggle="tab" data-bs-target="#panel-finance"
                        type="button" role="tab" aria-controls="panel-finance" aria-selected="false">Finance</button>
                    <button class="nav-link" id="nav-researchdesign-tab" data-bs-toggle="tab"
                        data-bs-target="#panel-researchdesign" type="button" role="tab" aria-controls="panel-researchdesign"
                        aria-selected="false">Research Design</button>
                    <button class="nav-link" id="nav-workplan-tab" data-bs-toggle="tab" data-bs-target="#panel-workplan"
                        type="button" role="tab" aria-controls="panel-workplan" aria-selected="false">Workplan</button>

                    @if (isset($prop) && !$prop->submittedstatus)
                        <button class="nav-link" id="nav-submit-tab" data-bs-toggle="tab" data-bs-target="#panel-submit"
                            type="button" role="tab" aria-controls="panel-submit" aria-selected="false">Submit</button>
                    @endif





                </div>
            </nav>

            <!-- Tab panes -->
            <div class="tab-content prop-tabpanel">
                <!-- Basic Details -->
                <div role="tabpanel" class="tab-pane active" id="panel-basicdetails">

                    <!-- Personal Details Form -->
                    <form method="POST" id="basicdetails"
                        action="{{ isset($prop) ? route('route.proposals.updatebasicdetails', ['id' => $prop->proposalid]) : route('route.proposals.post') }}"
                        enctype="multipart/form-data" class="form-horizontal">
                        @csrf
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Full Name</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="fullname" name="fullname" placeholder="Your Full Name"
                                    value="{{isset($isnewprop) ? auth()->user()->name : (isset($prop) ? $prop->applicant->name : '') }}"
                                    class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Email</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="email" name="email" placeholder="Your Email"
                                    value="{{isset($isnewprop) ? auth()->user()->email : (isset($prop) ? $prop->applicant->email : '') }}"
                                    class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">PF Number</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="pfnofk" name="pfnofk" placeholder="Your PF Number"
                                    value="{{isset($isnewprop) ? auth()->user()->pfno : (isset($prop) ? $prop->applicant->pfno : '') }}"
                                    class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Grant Number</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <select type="text" id="grantnofk" name="grantnofk" class="form-control">
                                    <option value="">Select a Grant Item</option>
                                    @foreach ($grants as $grant)
                                                                                <option value="{{ $grant->grantid }}" {{ (isset($prop) && $prop->grantnofk == $grant->grantid) ? 'selected' : '' }}>
                                            {{ $grant->grantid . ' - (' . $grant->title . ')'}}
                                        </option>
                                    @endforeach 
                                                            </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Research Theme</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <select type="text" id="themefk" name="themefk" class="form-control">
                                    <option value="">Select a Theme</option>
                                    @foreach ($themes as $theme)

                                                                                <option value="{{ $theme->themeid }}" {{ (isset($prop) && $prop->themefk == $theme->themeid) ? 'selected' : '' }}>
                                            {{ $theme->themename}}
                                        </option>

                                    @endforeach 
                                                                    </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Department Name</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <select type="text" id="departmentfk" name="departmentfk" placeholder="Department"
                                    class="form-control">
                                    <option value="">Select a Department</option>
                                    @foreach ($departments as $department)

                                                                                <option value="{{ $department->depid }}" {{ (isset($prop) && $prop->departmentidfk == $department->depid) ? 'selected' : '' }}>
                                            {{ $department->school->schoolname }} - {{ $department->shortname }}
                                        </option>

                                    @endforeach 
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Highest Qualification</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="highestqualification" name="highestqualification"
                                    placeholder="Highest Qualification" class="form-control"
                                    value="{{ isset($prop) ? $prop->highqualification : '' }}">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Office Telephone</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="officephone" name="officephone" placeholder="Office Telephone"
                                    class="form-control" value="{{ isset($prop) ? $prop->officephone : '' }}">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Cellphone</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="cellphone" name="cellphone" placeholder="Cellphone"
                                    class="form-control" value="{{ isset($prop) ? $prop->cellphone : '' }}">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Fax Number</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="faxnumber" name="faxnumber" placeholder="Fax Number"
                                    class="form-control" value="{{ isset($prop) ? $prop->faxnumber : '' }}">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-lg-6 col-md-6 col-sm-12 offset-lg-4 offset-md-4">
                                <button id="submitbasic_button" type="submit" class="btn btn-primary"
                                    style="width:200px; margin-top:8px;">
                                    Save Basic Details
                                </button>
                            </div>
                        </div>
                    </form>


                </div>


                <!-- Research tab -->
                <div role="tabpanel" class="tab-pane" id="panel-research">
                    <form id="form_researchinfo" method="POST"
                        action="{{ isset($prop) ? route('route.proposals.updateresearchdetails', ['id' => $prop->proposalid]) : route('route.proposals.post') }}"
                        enctype="multipart/form-data" class="form-horizontal">
                        @csrf
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Research Title</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="researchtitle" name="researchtitle" placeholder="Research Title"
                                    class="form-control" value="{{ isset($prop) ? $prop->researchtitle : '' }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label class=" form-control-label">
                                    Commencing Date</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="date" id="commencingdate" name="commencingdate" placeholder="DD/MM/YYYY"
                                    class="form-control" value="{{isset($prop) ? $prop->commencingdate : '' }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label class=" form-control-label">
                                    Termination Date</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="date" id="terminationdate" name="terminationdate" placeholder="DD/MM/YYYY"
                                    class="form-control" value="{{isset($prop) ? $prop->terminationdate : ''}}">
                            </div>
                        </div>
                        <script>
                            $(document).ready(function () {
                                // Get today's date in YYYY-MM-DD format
                                const today = new Date().toISOString().split('T')[0];

                                // Set the min attribute of the date input to today's date
                                document.getElementById('commencingdate').setAttribute('min', today);
                                document.getElementById('terminationdate').setAttribute('min', today);
                            });
                        </script>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Objectives</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <textarea name="objectives" placeholder="Objectives"
                                    class="form-control">{{ isset($prop) ? $prop->objectives : '' }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Question/Hypothesis</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <textarea name="hypothesis" placeholder="Question or Hypothesis"
                                    class="form-control">{{ isset($prop) ? $prop->hypothesis : '' }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Significance</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <textarea name="significance" placeholder="Significance or Justification"
                                    class="form-control">{{ isset($prop) ? $prop->significance : '' }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Ethical Considerations</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <textarea name="ethicals" placeholder="Ethical Considerations"
                                    class="form-control">{{ isset($prop) ? $prop->ethicals : '' }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Expected Outputs</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <textarea name="outputs" placeholder="Expected Outputs"
                                    class="form-control">{{ isset($prop) ? $prop->expoutput : '' }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Socio-Economic Impact</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <textarea name="economicimpact" placeholder="Socio-Economic Impact"
                                    class="form-control">{{ isset($prop) ? $prop->socio_impact : '' }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Research Findings</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <textarea name="res_findings" placeholder="Dissemination of Research Findings"
                                    class="form-control">{{ isset($prop) ? $prop->res_findings : '' }}</textarea>
                            </div>
                        </div>
                    </form>
                    <div class="row form-group">
                        <div class="col-lg-6 col-md-6 col-sm-12 offset-lg-4 offset-md-4">
                            <button id="saveresearchinfobutton" form="form_researchinfo" type="submit"
                                class="btn btn-primary" style="width:200px; margin-top:8px;">
                                Save Research Details
                            </button>
                        </div>
                    </div>

                </div>


                <!-- Collaborators tab -->
                <div role="tabpanel" class="tab-pane" id="panel-collaboration">
                    <style>
                        .nav-tabs .nav-link {
                            flex: 1;
                            /* Ensure each nav-link takes up an equal portion of the nav-tabs */
                            text-align: center;
                            /* Center the text within each nav-link */
                        }
                    </style>


                    <!-- Buttons to add collaborator and publication -->
                    <form class="form form-group">
                        @csrf
                        <div class="row form-group">
                            <!-- Button trigger modal -->
                            <div class="col-lg-2 col-md-3 col-sm-12">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#collaboratormodal">
                                    Add Collaborator
                                </button>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-12">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#publicationmodal">
                                    Add Publication
                                </button>
                            </div>

                        </div>
                    </form>
                    <!-- Collaborators details table -->
                    <div class="row form-group">
                        <div>Collaborators</div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <table id="collaboratorstable"
                                class="table table-responsive table-bordered table-striped table-hover" style="margin:4px">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Position</th>
                                        <th scope="col">Institution</th>
                                        <th scope="col">Research Area</th>
                                        <th scope="col">Experience</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <!-- publications table -->
                    <div class="row form-group">
                        <div>Publications</div>
                        <div style="max-height: 300px; overflow-y: auto;">

                            <table id="publicationstable"
                                class="table table-responsive table-bordered table-striped table-hover" style="margin:4px">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th scope="col">Authors (s)</th>
                                        <th scope="col">Year</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Research Area</th>
                                        <th scope="col">Experience</th>
                                        <th scope="col">Publisher</th>
                                        <th scope="col">Volume</th>
                                        <th scope="col">Pages</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>



                    <!--Collaborator Modal -->
                    <div class="modal fade" id="collaboratormodal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Add Collaborator</h5>
                                    <button type="button" id="closecollaboratormodal_button" class="btn-close"
                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form_collaborators">
                                        @csrf
                                        <!-- Collaborators details form fields -->
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Collaborator Name</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="collaboratorname" placeholder="Collaborator Name"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Position</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="position" placeholder="Position"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Institution</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="institution" placeholder="Institution"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Research Area</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="researcharea" placeholder="Research Area"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Research Experience</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="experience" placeholder="Research Experience"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="addCollaboratorButton" class="btn btn-success">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Publication Modal -->
                    <div class="modal fade" id="publicationmodal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="publicationLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="publicationLabel">Add New Publication</h5>
                                    <button type="button" id="closepublication_button" class="btn-close"
                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form_publications">
                                        @csrf
                                        <!-- Collaborators details form fields -->
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Authors (s)</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="authors" placeholder="Authors"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Year</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="year" placeholder="Year" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Title</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="pubtitle" placeholder="Title" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Research Area</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="researcharea" placeholder="Research Area"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Publisher</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="publisher" placeholder="Publisher"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Volume</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="volume" placeholder="Volume" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col col-md-3">
                                                <label class="form-control-label">Pages</label>
                                            </div>
                                            <div class="col-12 col-md-9">
                                                <input type="text" name="pubpages" placeholder="Pages" class="form-control">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button id="addpublicationButton" type="submit" class="btn btn-success"
                                        style="width:200px">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <script>
                        $(document).ready(function () {

                            let proposalId = "{{ isset($prop) ? $prop->proposalid : '' }}"; // Check if proposalId is set
                            // Assuming prop is passed to the Blade view from the Laravel controller
                            const collaboratorsurl = `{{ route('api.proposals.fetchcollaborators', ['id' => ':id']) }}`.replace(':id', proposalId);
                            const punlicationsurl = `{{ route('api.proposals.fetchpublications', ['id' => ':id']) }}`.replace(':id', proposalId);

                            document.getElementById('addCollaboratorButton')?.addEventListener('click', function () {

                                var formData = $('#form_collaborators').serialize();
                                if (proposalId) {
                                    formData += '&proposalidfk=' + proposalId;
                                }
                                // Function to fetch data using AJAX
                                $.ajax({
                                    url: "{{ route('api.collaborators.post') }}",
                                    type: 'POST',
                                    data: formData,
                                    dataType: 'json',
                                    success: function (response) {
                                        showtoastmessage(response);
                                        // Close the modal
                                        var button = document.getElementById('closecollaboratormodal_button');
                                        if (button) {
                                            button.click();
                                        }
                                        fetchcollaborators();
                                    },
                                    error: function (xhr, status, error) {
                                        var mess = JSON.stringify(xhr.responseJSON.message);
                                        var type = JSON.stringify(xhr.responseJSON.type);
                                        var result = {
                                            message: mess,
                                            type: type
                                        };
                                        showtoastmessage(result);

                                        console.error('Error fetching data:', error);
                                    }
                                });
                            });

                            document.getElementById('addpublicationButton')?.addEventListener('click', function () {
                                var tablecontainer = document.getElementById('colltable');

                                var formData = $('#form_publications').serialize();
                                if (proposalId) {
                                    formData += '&proposalidfk=' + proposalId;
                                }
                                // Function to fetch data using AJAX
                                $.ajax({
                                    url: "{{ route('api.publications.post') }}",
                                    type: 'POST',
                                    data: formData,
                                    dataType: 'json',
                                    success: function (response) {
                                        showtoastmessage(response);
                                        // Close the modal
                                        var button = document.getElementById('closepublication_button');
                                        if (button) {
                                            button.click();
                                        }
                                        fetchpublications();
                                    },
                                    error: function (xhr, status, error) {
                                        var mess = JSON.stringify(xhr.responseJSON.message);
                                        var type = JSON.stringify(xhr.responseJSON.type);
                                        var result = {
                                            message: mess,
                                            type: type
                                        };
                                        showtoastmessage(result);
                                        console.error('Error fetching data:', error);
                                    }
                                });
                            });

                            function showtoastmessage1(response) {


                                var toastEl = document.getElementById('liveToast');
                                if (toastEl) {
                                    var toastbody = document.getElementById('toastmessage_body');
                                    var toastheader = document.getElementById('toastheader');
                                    toastheader.classList.remove('bg-primary', 'bg-success', 'bg-danger', 'bg-info', 'bg-warning', 'bg-secondary');


                                    if (response && response.type) {
                                        if (response.type == "success") {
                                            toastheader.classList.add('bg-success');
                                        }
                                        else if (response.type == "warning") {
                                            toastheader.classList.add('bg-warning');
                                        }
                                        else {
                                            toastheader.classList.add('bg-danger');
                                        }
                                    }
                                    else {
                                        toastheader.classList.add('bg-danger');
                                    }
                                    toastbody.innerText = response && response.message ? response.message : "No Message";
                                    var toast = new bootstrap.Toast(toastEl, {
                                        autohide: true,
                                        delay: 2000
                                    });
                                    toast.show();
                                }
                            }
                            // Function to fetch collaborators data 
                            function fetchcollaborators() {
                                $.ajax({
                                    url: collaboratorsurl,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        populatecollaborators(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching data:', error);
                                    }
                                });
                            }

                            // Function to populate collaborators
                            function populatecollaborators(data) {
                                var tbody = $('#collaboratorstable tbody');
                                tbody.empty(); // Clear existing table rows
                                if (data.length > 0) {
                                    $.each(data, function (index, data) {
                                        var row = '<tr>' +
                                            '<td>' + data.collaboratorname + '</td>' +
                                            '<td>' + data.position + '</td>' +
                                            '<td>' + data.institution + '</td>' +
                                            '<td>' + data.researcharea + '</td>' +
                                            '<td>' + data.experience + '</td>' +
                                            '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                                            '<td>Edit</td>' +
                                            '</tr>';
                                        tbody.append(row);
                                    });
                                }
                                else {
                                    var row = '<tr><td colspan="7" class="text-center">No Collaborators found</td></tr>';
                                    tbody.append(row);
                                }
                            }


                            function fetchpublications() {
                                $.ajax({
                                    url: punlicationsurl,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        populatepublications(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching data:', error);
                                    }
                                });
                            }
                            // Function to populate collaborators
                            function populatepublications(data) {
                                var tbody = $('#publicationstable tbody');
                                tbody.empty(); // Clear existing table rows
                                if (data.length > 0) {
                                    $.each(data, function (index, data) {
                                        var row = '<tr>' +
                                            '<td>' + data.authors + '</td>' +
                                            '<td>' + data.year + '</td>' +
                                            '<td>' + data.title + '</td>' +
                                            '<td>' + data.researcharea + '</td>' +
                                            '<td>' + data.publisher + '</td>' +
                                            '<td>' + data.volume + '</td>' +
                                            '<td>' + data.pages + '</td>' +
                                            '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                                            '<td>Edit</td>' +
                                            '</tr>';
                                        tbody.append(row);
                                    });
                                }
                                else {
                                    var row = '<tr><td colspan="9" class="text-center">No Publications found</td></tr>';
                                    tbody.append(row);
                                }
                            }
                            fetchcollaborators();
                            fetchpublications();
                        });
                    </script>


                </div>


                <!-- Finance Tab -->
                <div role="tabpanel" class="tab-pane " id="panel-finance">
                    <form id="form_finance" enctype="multipart/form-data" class="form-horizontal">
                        @csrf
                        <div class="row form-group">

                            <div class="form-group col-lg-3 -col-md-3">
                                <label class=" form-control-label">Equipments Cost</label>
                                <input type="number" id="equipmentscost" placeholder="0.00" readonly disabled
                                    class="form-control">
                            </div>
                            <div class="form-group col-lg-3 -col-md-3">
                                <label class=" form-control-label">Consumables Cost</label>
                                <input type="number" id="consumablescost" placeholder="0.00" readonly disabled
                                    class="form-control">
                            </div>
                            <div class="form-group col-lg-3 -col-md-3">
                                <label class=" form-control-label">Travel Cost</label>
                                <input type="number" id="travelcost" placeholder="0.00" readonly disabled
                                    class="form-control">
                            </div>
                            <div class="form-group col-lg-3 -col-md-3">
                                <label class=" form-control-label">Other Cost</label>
                                <input type="number" id="othercost" placeholder="0.00" readonly disabled
                                    class="form-control">
                            </div>
                            <div class="form-group col-lg-3 -col-md-3">
                                <style>
                                    .bold-input {
                                        font-weight: bold;
                                        /* Additional styles if needed */
                                    }
                                </style>
                                <label class=" form-control-label">Total Funds</label>
                                <input type="text" id="totalfunds" placeholder="0.00" readonly disabled
                                    class="form-control bold-input">
                            </div>
                            <div class="form-group col-lg-3 -col-md-3">
                                <label  class="form-control-label">60/40 Rule Valid?</label>
                                <input type="text" id="budgetvalidstatuslabel" placeholder="Status" readonly disabled
                                class="form-control bold-input">
                            </div>
                            <div class="form-group col-lg-3 -col-md-3">
                                <label class="form-control-label"></label>
                                <!-- Trigger Button -->
                                <button type="button" class="form-control btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#expenditureModal">
                                    Add Expenditure
                                </button>
                            </div>
                        </div>

                        <div class="row form-group">

                        </div>
                        <!-- Modal to add expenditure -->

                        <div class="row form-group">
                            <h5>Expenditure Breakdown</h5>
                        </div>
                    </form>
                    <div>

                        <table id="expenditurestable"
                            class="table table-responsive table-bordered table-striped table-hover" style="margin:4px">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th scope="col">Item</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="container mt-5">

                        <!-- Modal -->
                        <div class="modal fade" id="expenditureModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Expenditure Details</h5>
                                        <button type="button" id="btn_close_expendituremodal" class="close"
                                            data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="expenditureForm">
                                            @csrf
                                            <div class="form-group">
                                                <label for="item">Item</label>
                                                <input type="text" class="form-control" id="item" name="item" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="itemtype">Expenditure Type</label>
                                                <select class="form-control" id="itemtype" name="itemtype" required>
                                                    <option value="">Select Expenditure Type</option>
                                                    <option value="Facilities">Facilities & Equipments</option>
                                                    <option value="Consumables">Consumables</option>
                                                    <option value="Travels">Travel & Substinence</option>
                                                    <option value="Others">Personal &Other Cost</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="quantity">Quantity</label>
                                                <input type="number" class="form-control" id="quantity" name="quantity"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="unitprice">Price</label>
                                                <input type="number" step="0.01" class="form-control" id="unitprice"
                                                    name="unitprice" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="total">Total</label>
                                                <input type="number" step="0.01" class="form-control" id="total"
                                                    name="total" readonly>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" id="button_save_expenditure"
                                                    class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).ready(function () {
                                // Calculate total when quantity or price changes
                                document.getElementById('quantity')?.addEventListener('input', calculateTotal);
                                document.getElementById('unitprice')?.addEventListener('input', calculateTotal);
                                var closebtn = document.getElementById('btn_close_expendituremodal');

                                function calculateTotal() {
                                    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
                                    const price = parseFloat(document.getElementById('unitprice').value) || 0;
                                    const total = quantity * price;
                                    document.getElementById('total').value = total.toFixed(2);
                                }




                                let proposalId = "{{ isset($prop) ? $prop->proposalid : '' }}"; // Check if proposalId is set
                                // Assuming prop is passed to the Blade view from the Laravel controller
                                const expenditureurl = `{{ route('api.proposals.fetchexpenditures', ['id' => ':id']) }}`.replace(':id', proposalId);

                                document.getElementById('button_save_expenditure')?.addEventListener('click', function () {

                                    var formData = $('#expenditureForm').serialize();
                                    if (proposalId) {
                                        formData += '&proposalidfk=' + proposalId;
                                    }
                                    // Function to fetch data using AJAX
                                    $.ajax({
                                        url: "{{ route('api.expenditures.post') }}",
                                        type: 'POST',
                                        data: formData,
                                        dataType: 'json',
                                        success: function (response) {
                                            if (closebtn) { closebtn.click(); }
                                            showtoastmessage(response);

                                            fetchexpenditures();
                                        },
                                        error: function (xhr, status, error) {
                                            var mess = JSON.stringify(xhr.responseJSON.message);
                                            var type = JSON.stringify(xhr.responseJSON.type);
                                            var result = {
                                                message: mess,
                                                type: type
                                            };
                                            showtoastmessage(result);

                                            console.error('Error fetching data:', error);
                                        }
                                    });
                                });

                                // Function to fetch expenditures data 
                                function fetchexpenditures() {
                                    $.ajax({
                                        url: expenditureurl,
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function (response) {
                                            populateexpenditures(response);
                                            populatetopfields(response);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('Error fetching data:', error);
                                        }
                                    });
                                }

                                function populatetopfields(response) {
                                    if (response?.data?.length > 0) {
                                        let travel = 0;
                                        let consumables = 0;
                                        let facilities = 0;
                                        let others = 0;
                                        let total = 0;
                                        $.each(response?.data, function (index, data) {
                                            total += parseFloat(data.total);
                                            if (data.itemtype == "Facilities") {
                                                facilities += parseFloat(data.total);
                                            }
                                            else if (data.itemtype == "Consumables") {
                                                consumables += parseFloat(data.total);
                                            }
                                            else if (data.itemtype == "Travels") {
                                                travel += parseFloat(data.total);
                                            }
                                            else if (data.itemtype == "Others") {
                                                others += parseFloat(data.total);
                                            }
                                        });
                                        document.getElementById('totalfunds').value = total;
                                        document.getElementById('travelcost').value = travel;
                                        document.getElementById('equipmentscost').value = facilities;
                                        document.getElementById('othercost').value = others;
                                        document.getElementById('consumablescost').value = consumables;
                                        document.getElementById('budgetvalidstatuslabel').value = response?.summary?.isValidBudget?.toString();
                                        };
                                    }

                            // Function to populate collaborators
                            function populateexpenditures(response) {
                                var tbody = $('#expenditurestable tbody');
                                tbody.empty(); // Clear existing table rows
                                if (response?.data?.length > 0) {
                                    $.each(response.data, function (index, data) {
                                        console.log(data);

                                        var row = '<tr>' +
                                            '<td>' + data.item + '</td>' +
                                            '<td>' + data.itemtype + '</td>' +
                                            '<td>' + data.quantity + '</td>' +
                                            '<td>' + data.unitprice + '</td>' +
                                            '<td>' + data.total + '</td>' +
                                            '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                                            '<td>Edit</td>' +
                                            '</tr>';
                                        tbody.append(row);
                                    });
                                }
                                else {
                                    var row = '<tr><td colspan="6" class="text-center">No Expenditures found</td></tr>';
                                    tbody.append(row);
                                }
                            }

                            fetchexpenditures();
                                });
                        </script>
                    </div>

                </div>

                <!-- Research Design -->
                <div role="tabpanel" class="tab-pane" id="panel-researchdesign">
                    <!-- Research design details form -->
                    <form id="form_researchdesign" method="POST" class="form-horizontal">
                        @csrf
                        <!-- Collaborators details form fields -->
                        <div class="row form-group">
                            <div class="col-lg-3 col-md-3 col-sm-4">
                                <label class=" form-control-label">Project Summary</label>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-8">
                                <input type="text" name="projectsummary" placeholder="Project Summary" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-3 col-md-3 col-sm-4">
                                <label class=" form-control-label">Indicators</label>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-8">
                                <input type="text" name="indicators" placeholder="Measurable Indicators"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-3 col-md-3 col-sm-4">
                                <label class=" form-control-label">Verification</label>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-8">
                                <input type="text" name="verification" placeholder="Means of Verification"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-3 col-md-3 col-sm-4">
                                <label class=" form-control-label">Assumptions</label>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-8">
                                <input type="text" name="assumptions" placeholder="Important Assumptions"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-3 col-md-3 col-sm-4">
                                <label class=" form-control-label">Goal</label>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-8">
                                <input type="text" name="goal" placeholder="Goal" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-3 col-md-3 col-sm-4">
                                <label class=" form-control-label">Purpose</label>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-8">
                                <input type="text" name="purpose" placeholder="Purpose" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col text-center">
                                <button id="saveresearchdesignButton" type="button" class="btn btn-primary ">Add Research
                                    Design Item</button>
                            </div>
                        </div>
                    </form>
                    <div class="row form-group">
                        <h5 class="mt-2">Research Design Items</h5>
                    </div>
                    <div class="row form-group">
                        <table id="reserchdesignitemstable"
                            class="table table-responsive table-bordered table-striped table-hover" style="margin:4px">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th scope="col">Summary</th>
                                    <th scope="col">Indicators</th>
                                    <th scope="col">Verification</th>
                                    <th scope="col">Assumptions</th>
                                    <th scope="col">Goal</th>
                                    <th scope="col">Purpose</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <script>
                        $(document).ready(function () {


                            let proposalId = "{{ isset($prop) ? $prop->proposalid : '' }}"; // Check if proposalId is set
                            // Assuming prop is passed to the Blade view from the Laravel controller
                            const researchurl = `{{ route('api.proposals.researchdesignitems', ['id' => ':id']) }}`.replace(':id', proposalId);

                            document.getElementById('saveresearchdesignButton')?.addEventListener('click', function () {

                                var formData = $('#form_researchdesign').serialize();
                                if (proposalId) {
                                    formData += '&proposalidfk=' + proposalId;
                                }
                                // Function to fetch data using AJAX
                                $.ajax({
                                    url: "{{ route('api.researchdesign.post') }}",
                                    type: 'POST',
                                    data: formData,
                                    dataType: 'json',
                                    success: function (response) {
                                        showtoastmessage(response);
                                        fetchresearchdesign();
                                    },
                                    error: function (xhr, status, error) {
                                        var mess = JSON.stringify(xhr.responseJSON.message);
                                        var type = JSON.stringify(xhr.responseJSON.type);
                                        var result = {
                                            message: mess,
                                            type: type
                                        };
                                        showtoastmessage(result);

                                        console.error('Error fetching data:', error);
                                    }
                                });
                            });


                            function showtoastmessage(response) {


                                var toastEl = document.getElementById('liveToast');
                                if (toastEl) {
                                    var toastbody = document.getElementById('toastmessage_body');
                                    var toastheader = document.getElementById('toastheader');
                                    toastheader.classList.remove('bg-primary', 'bg-success', 'bg-danger', 'bg-info', 'bg-warning', 'bg-secondary');

                                    if (response && response.type) {
                                        if (response.type == "success") {
                                            toastheader.classList.add('bg-success');
                                        }
                                        else if (response.type == "warning") {
                                            toastheader.classList.add('bg-warning');
                                        }
                                        else {
                                            toastheader.classList.add('bg-danger');
                                        }
                                    }
                                    else {
                                        toastheader.classList.add('bg-danger');
                                    }
                                    toastbody.innerText = response && response.message ? response.message : "No Message";
                                    var toast = new bootstrap.Toast(toastEl, {
                                        autohide: true,
                                        delay: 2000
                                    });
                                    toast.show();
                                }
                            }

                            // Function to fetch expenditures data 
                            function fetchresearchdesign() {
                                $.ajax({
                                    url: researchurl,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        populateresearchdesignitems(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching data:', error);
                                    }
                                });
                            }

                            // Function to populate collaborators
                            function populateresearchdesignitems(data) {
                                var tbody = $('#reserchdesignitemstable tbody');
                                tbody.empty(); // Clear existing table rows
                                if (data.length > 0) {
                                    $.each(data, function (index, data) {
                                        var row = '<tr>' +
                                            '<td>' + data.summary + '</td>' +
                                            '<td>' + data.indicators + '</td>' +
                                            '<td>' + data.verification + '</td>' +
                                            '<td>' + data.assumptions + '</td>' +
                                            '<td>' + data.goal + '</td>' +
                                            '<td>' + data.purpose + '</td>' +
                                            '</tr>';
                                        tbody.append(row);
                                    });
                                }
                                else {
                                    var row = '<tr><td colspan="6" class="text-center">No Research Design Items found</td></tr>';
                                    tbody.append(row);
                                }
                            }

                            fetchresearchdesign();
                        });
                    </script>
                </div>

                <!-- Workplan -->
                <div role="tabpanel" class="tab-pane" id="panel-workplan">
                    <form id="form_workplan">
                        @csrf
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class=" form-control-label">Activity</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" name="activity" placeholder="Activity undertaken" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class=" form-control-label">Time</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" name="time" placeholder="Time" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class=" form-control-label">Input</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" name="input" placeholder="Input" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label class=" form-control-label">Facilities</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" name="facilities" placeholder="Facilities " class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label class=" form-control-label">By Whom</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" name="bywhom" placeholder="By Whom" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label class=" form-control-label">Outcome</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" name="outcome" placeholder="Outcome" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col text-center">
                                <button id="saveworkplanbutton" type="button" class="btn btn-primary ">Add Workplan
                                    Item</button>

                            </div>
                        </div>
                    </form>
                    <div class="row form-group">
                        <h5 class="mt-1">Workplan Items</h5>
                    </div>
                    <div class="row form-group">
                        <table id="workplaitemstable"
                            class="table table-responsive table-bordered table-striped table-hover" style="margin:4px">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th scope="col">Activity</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Input</th>
                                    <th scope="col">Facilities</th>
                                    <th scope="col">By Whom</th>
                                    <th scope="col">Outcome</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <script>
                        $(document).ready(function () {


                            let proposalId = "{{ isset($prop) ? $prop->proposalid : '' }}"; // Check if proposalId is set
                            // Assuming prop is passed to the Blade view from the Laravel controller
                            const workplanurl = `{{ route('api.proposals.fetchworkplanitems', ['id' => ':id']) }}`.replace(':id', proposalId);

                            document.getElementById('saveworkplanbutton')?.addEventListener('click', function () {

                                var formData = $('#form_workplan').serialize();
                                if (proposalId) {
                                    formData += '&proposalidfk=' + proposalId;
                                }
                                // Function to fetch data using AJAX
                                $.ajax({
                                    url: "{{ route('api.workplan.post') }}",
                                    type: 'POST',
                                    data: formData,
                                    dataType: 'json',
                                    success: function (response) {
                                        showtoastmessage(response);
                                        fetchworkplanitems();
                                    },
                                    error: function (xhr, status, error) {
                                        var mess = JSON.stringify(xhr.responseJSON.message);
                                        var type = JSON.stringify(xhr.responseJSON.type);
                                        var result = {
                                            message: mess,
                                            type: type
                                        };
                                        showtoastmessage(result);

                                        console.error('Error fetching data:', error);
                                    }
                                });
                            });


                            function showtoastmessage(response) {


                                var toastEl = document.getElementById('liveToast');
                                if (toastEl) {
                                    var toastbody = document.getElementById('toastmessage_body');
                                    var toastheader = document.getElementById('toastheader');
                                    toastheader.classList.remove('bg-primary', 'bg-success', 'bg-danger', 'bg-info', 'bg-warning', 'bg-secondary');

                                    if (response && response.type) {
                                        if (response.type == "success") {
                                            toastheader.classList.add('bg-success');
                                        }
                                        else if (response.type == "warning") {
                                            toastheader.classList.add('bg-warning');
                                        }
                                        else {
                                            toastheader.classList.add('bg-danger');
                                        }
                                    }
                                    else {
                                        toastheader.classList.add('bg-danger');
                                    }
                                    toastbody.innerText = response && response.message ? response.message : "No Message";
                                    var toast = new bootstrap.Toast(toastEl, {
                                        autohide: true,
                                        delay: 2000
                                    });
                                    toast.show();
                                }
                            }

                            // Function to fetch expenditures data 
                            function fetchworkplanitems() {
                                $.ajax({
                                    url: workplanurl,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        populateworkplanitems(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching data:', error);
                                    }
                                });
                            }

                            // Function to populate collaborators
                            function populateworkplanitems(data) {
                                var tbody = $('#workplaitemstable tbody');
                                tbody.empty(); // Clear existing table rows
                                if (data.length > 0) {
                                    $.each(data, function (index, data) {
                                        var row = '<tr>' +
                                            '<td>' + data.activity + '</td>' +
                                            '<td>' + data.time + '</td>' +
                                            '<td>' + data.input + '</td>' +
                                            '<td>' + data.facilities + '</td>' +
                                            '<td>' + data.bywhom + '</td>' +
                                            '<td>' + data.outcome + '</td>' +
                                            '</tr>';
                                        tbody.append(row);
                                    });
                                }
                                else {
                                    var row = '<tr><td colspan="6" class="text-center">No WorkPlan Items found</td></tr>';
                                    tbody.append(row);
                                }
                            }

                            fetchworkplanitems();
                        });
                    </script>
                </div>

                <!-- Submit -->
                <div role="tabpanel" class="tab-pane" id="panel-submit">
                    @csrf
                    <div class="row form-group">
                        <h5 class="mt-2 text-center">Submit Your Application</h5>

                    </div>
                    <div class="row form-group">
                        <div class="col-12">
                            <table id="progresstable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 60%;">Item</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span style="color:red">* </span>Basic Details</td>
                                        <td id="td_basic">Incomplete</td>
                                    </tr>
                                    <tr>
                                        <td><span style="color:red">* </span>Research Details</td>
                                        <td id="td_researchinfo">Incomplete</td>
                                    </tr>
                                    <tr>
                                        <td>Collaborators</td>
                                        <td id="td_col">Not Filled</td>
                                    </tr>
                                    <tr>
                                        <td>Publications</td>
                                        <td id="td_pub">Not Filled</td>
                                    </tr>
                                    <tr>
                                        <td><span style="color:red">* </span>Financials</td>
                                        <td id="td_financials">Incomplete</td>
                                    </tr>
                                    <tr>
                                        <td><span style="color:red">* </span>Research Design</td>
                                        <td id="td_design">Incomplete</td>
                                    </tr>
                                    <tr>
                                        <td><span style="color:red">* </span>Workplan</td>
                                        <td id="td_workplan">Incomplete</td>
                                    </tr>
                                    <!-- Add more rows as needed -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col text-center">
                            <button id="btn_refreshstatus" type="button" class="btn btn-info text-light "
                                style="width:200px; margin-top:8px;">Check Status</button>

                        </div>
                        <div class="col text-center">
                            <button id="btn_submitapplication" type="button" class="btn btn-success text-light "
                                style="width:200px; margin-top:8px;" disabled>Submit</button>

                        </div>
                    </div>
                    <script>
                        $(document).ready(function () {
                            var refreshbutton = document.getElementById('btn_refreshstatus');
                            var submit = document.getElementById('btn_submitapplication');
                            var td_basic = document.getElementById('td_basic');
                            var td_researchinfo = document.getElementById('td_researchinfo');
                            var td_collaborators = document.getElementById('td_col');
                            var td_publications = document.getElementById('td_pub');
                            var td_workplan = document.getElementById('td_workplan');
                            var td_design = document.getElementById('td_design');
                            var td_financials = document.getElementById('td_financials');
                            refreshbutton.addEventListener('click', function () {
                                fetchapplicationstatus();
                            });
                            submit.addEventListener('click', function () {
                                // Get the button element
                                var button = this;

                                // Disable the button to prevent double clicks
                                button.disabled = true;

                                var csrfToken = document.getElementsByName('_token')[0].value;
                                $.ajax({
                                    url: submiturl,
                                    type: 'POST',
                                    data: { _token: csrfToken },
                                    dataType: 'json',
                                    success: function (response) {
                                        showtoastmessage(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching data:', error);
                                    }
                                });

                            });
                            let proposalId = "{{isset($prop) ? $prop->proposalid : ''}}"
                            const statusurl = `{{ route('api.proposals.submissionstatus', ['id' => ':id']) }}`.replace(':id', proposalId);
                            const submiturl = `{{ route('api.proposals.submitproposal', ['id' => ':id']) }}`.replace(':id', proposalId);

                            function fetchapplicationstatus() {
                                $.ajax({
                                    url: statusurl,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        td_basic.innerText = response.data.basic == 2 ? 'Completed' : 'Incomplete';
                                        td_researchinfo.innerText = response.data.researchinfo == 2 ? 'Completed' : 'Incomplete';
                                        td_collaborators.innerText = response.data.collaborators == 2 ? 'Completed' : 'Incomplete';
                                        td_publications.innerText = response.data.publications == 2 ? 'Completed' : 'Incomplete';
                                        td_financials.innerText = response.data.expenditure == 2 ? 'Completed' : 'Incomplete';
                                        td_design.innerText = response.data.design == 2 ? 'Completed' : 'Incomplete';
                                        td_workplan.innerText = response.data.workplan == 2 ? 'Completed' : 'Incomplete';


                                        // Select the table by ID
                                        const table = document.getElementById('progresstable');
                                        const allCells = table.querySelectorAll('table td');
                                        const matchingCells = [];

                                        // Loop through all table cells and check their innerText
                                        allCells.forEach(cell => {
                                            if (cell.innerText.trim() === "Incomplete") {
                                                cell.classList.remove('text-dark', 'text-danger', 'font-weight-bold');
                                                cell.classList.add('text-danger', 'font-weight-bold');
                                            }
                                        });
                                        if (response.cansubmitstatus) {
                                            submit.disabled = false;
                                        }
                                        else {
                                            submit.disabled = true;
                                        }

                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching data:', error);
                                    }
                                });
                            }

                            function showtoastmessage(response) {


                                var toastEl = document.getElementById('liveToast');
                                if (toastEl) {
                                    var toastbody = document.getElementById('toastmessage_body');
                                    var toastheader = document.getElementById('toastheader');
                                    toastheader.classList.remove('bg-primary', 'bg-success', 'bg-danger', 'bg-info', 'bg-warning', 'bg-secondary');


                                    if (response && response.type) {
                                        if (response.type == "success") {
                                            toastheader.classList.add('bg-success');
                                        }
                                        else if (response.type == "warning") {
                                            toastheader.classList.add('bg-warning');
                                        }
                                        else {
                                            toastheader.classList.add('bg-danger');
                                        }
                                    }
                                    else {
                                        toastheader.classList.add('bg-danger');
                                    }
                                    toastbody.innerText = response && response.message ? response.message : "No Message";
                                    var toast = new bootstrap.Toast(toastEl, {
                                        autohide: true,
                                        delay: 2000
                                    });
                                    toast.show();
                                }
                            }


                        });


                    </script>
                </div>

            </div>
        </div>
        @endif
    </div>
@else





    <div class="col text-dark text-center">
        <br /><br /><br />
        <h4>There are no Open Calls for Grant this Year</h4>
        <br /><br />
        <a href="{{route('pages.home')}}">Go Back to Home</a>
    </div>
@endif 
@endsection