@extends('layouts.master')

@section('content')
    @auth
        <div>

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

            <div class="prop-tabcontainer">
                <!-- Nav tabs -->
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-grants-tab" data-bs-toggle="tab" data-bs-target="#panel-grants"
                            type="button" role="tab" aria-controls="panel-grants" aria-selected="true">Grants</button>
                        <button class="nav-link " id="nav-finyears-tab" data-bs-toggle="tab" data-bs-target="#panel-finyears"
                            type="button" role="tab" aria-controls="panel-finyears" aria-selected="true">Financial
                            Years</button>
                        @if (Auth()->user()->haspermission('canupdatecurrentgrantandyear'))
                            <button class="nav-link" id="nav-settings-tab" data-bs-toggle="tab" data-bs-target="#panel-settings"
                                type="button" role="tab" aria-controls="panel-settings"
                                aria-selected="false">Settings</button>
                        @endif
                    </div>
                </nav>

                <!-- Tab panes -->
                <div class="tab-content prop-tabpanel">

                    <!-- grants Details -->
                    <div role="tabpanel" class="tab-pane active" id="panel-grants">
                        <div class="row">
                            <style>
                                #searchInput::placeholder {
                                    color: #cdc8c8;
                                    /* Change to your desired color */
                                }
                            </style>

                            @auth

                                <div class="row form-group" style="padding-top:4px">

                                    <!--add grant Modal -->
                                    <div class="modal fade" id="addgrantmodal" data-bs-backdrop="static" data-bs-keyboard="false"
                                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="staticBackdropLabel">Add Call for Grants</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="form_addgrant" method="POST">
                                                        @csrf
                                                        <div class="row form-group">
                                                            <div class="col col-md-3">
                                                                <label class="form-control-label">Grant Title</label>
                                                            </div>
                                                            <div class="col-12 col-md-9">
                                                                <input type="text" name="title"
                                                                    placeholder="Title for the grant" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row form-group mt-2">
                                                            <div class="col col-md-3">
                                                                <label class="form-control-label">Financial Year</label>
                                                            </div>
                                                            <div class="col-12 col-md-9">
                                                                <select type="text" id="finyearfk" name="finyearfk"
                                                                    class="form-control">
                                                                    <option value="">Select a Financial Year</option>
                                                                    @foreach ($finyears as $year)
                                                                        <option value="{{ $year->id }}">
                                                                            {{ $year->finyear . ' - (' . $year->startdate . '/' . $year->enddate . ')' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group mt-2">
                                                            <div class="col col-md-3">
                                                                <label class="form-control-label">Status</label>
                                                            </div>
                                                            <div class="col-12 col-md-9">
                                                                <select type="text" name="status" class="form-control">

                                                                    <option>Open</option>
                                                                    <option>Closed</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button id="btn_closegrantmodal" type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button id="btn_savegrant" type="button" class="btn btn-primary">Save
                                                        Grant</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <form class="form-horizontal">
                                        <div class="row form-group">
                                            <div class="col-lg-10 col-md-9 col-sm-9 col-xs-6">

                                                <input type="text" id="searchInput" class="form-control text-center"
                                                    style="::placeholder { color: red; }"
                                                    placeholder="Search by Grant No, Year or Status">
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
                                                @if (auth()->user()->haspermission('canaddoreditgrant'))
                                                    <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                                        data-bs-target="#addgrantmodal">
                                                        Add Grant
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>

                                    <table id="grantstable"
                                        class="table table-responsive table-bordered table-striped table-hover"
                                        style="margin:4px">
                                        <thead class="bg-secondary text-white">
                                            <tr>
                                                <th scope="col">Grant No</th>
                                                <th scope="col">Title</th>
                                                <th scope="col">Fin Year</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Date Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            @endauth
                        </div>
                        <script>
                            $(document).ready(function() {

                                document.getElementById('btn_savegrant')?.addEventListener('click', function() {

                                    var formData = $('#form_addgrant').serialize();

                                    // Function to fetch data using AJAX
                                    $.ajax({
                                        url: "{{ route('api.grants.post') }}",
                                        type: 'POST',
                                        data: formData,
                                        dataType: 'json',
                                        success: function(response) {
                                            var closebtn = document.getElementById('btn_closegrantmodal');
                                            if (closebtn) {
                                                closebtn.click();
                                            }
                                            showtoastmessage(response);
                                            fetchgrantsData();
                                        },
                                        error: function(xhr, status, error) {
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
                                // Function to fetch data using AJAX
                                function fetchgrantsData() {
                                    $.ajax({
                                        url: "{{ route('api.grants.fetchallgrants') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function(response) {
                                            populateTable(response);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error fetching data:', error);
                                        }
                                    });
                                }

                                // Function to search data using AJAX
                                function searchData(searchTerm) {
                                    $.ajax({
                                        url: "{{ route('api.grants.fetchsearchgrants') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            search: searchTerm
                                        },
                                        success: function(response) {

                                            populateTable(response);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error searching data:', error);
                                        }
                                    });
                                }

                                var routeUrlTemplate = "{{ route('pages.grants.viewgrant', ['id' => '__ID__']) }}";
                                // Function to populate table with data
                                function populateTable(data) {
                                    var tbody = $('#grantstable tbody');
                                    tbody.empty(); // Clear existing table rows
                                    if (data.length > 0) {
                                        $.each(data, function(index, data) {
                                            var granturl = routeUrlTemplate.replace('__ID__', data.grantid);
                                            var row = '<tr>' +
                                                '<td>' + data.grantid + '</td>' +
                                                '<td><a class="nav-link" href="' + granturl + '">' + data.title + '</a></td>' +
                                                '<td>' + data.financialyear?.finyear + '</td>' +
                                                '<td>' + data.status + '</td>' +
                                                '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                                                '</tr>';
                                            tbody.append(row);
                                        });
                                    } else {
                                        var row = '<tr><td colspan="5" class="text-center text-dark"><b>No Grants found</b></td></tr>';
                                        tbody.append(row);
                                    }
                                }

                                // Initial fetch when the page loads
                                fetchgrantsData();

                                // Search input keyup event
                                $('#searchInput').on('keyup', function() {
                                    var searchTerm = $(this).val().toLowerCase();
                                    if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                                        searchData(searchTerm);
                                    } else if (searchTerm.length === 0) {
                                        fetchgrantsData(); // Fetch all data when search input is empty
                                    }
                                });
                            });
                        </script>
                    </div>

                    <!-- finyears Details -->
                    <div role="tabpanel" class="tab-pane" id="panel-finyears">
                        <div class="row">
                            <style>
                                #searchInput::placeholder {
                                    color: #cdc8c8;
                                    /* Change to your desired color */
                                }
                            </style>

                            @auth

                                <div class="row form-group" style="padding-top:4px">

                                    <!--add finyear Modal -->
                                    <div class="modal fade" id="addfinyearmodal" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="finyearmodalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="finyearmodalLabel">Add Financial Year</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="form_addfinyear" method="POST">
                                                        @csrf

                                                        <div class="row form-group mt-2">
                                                            <div class="col col-md-3">
                                                                <label class="form-control-label">Financial Year</label>
                                                            </div>
                                                            <div class="col-12 col-md-9">
                                                                <input type="text" id="finyear" name="finyear"
                                                                    placeholder="Financial Year" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row form-group mt-2">
                                                            <div class="col col-md-3">
                                                                <label class="form-control-label">Start Date</label>
                                                            </div>
                                                            <div class="col-12 col-md-9">
                                                                <input type="date" id="finyear_startdate" name="startdate"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row form-group mt-2">
                                                            <div class="col col-md-3">
                                                                <label class="form-control-label">End Date</label>
                                                            </div>
                                                            <div class="col-12 col-md-9">
                                                                <input type="date" id="finyear_enddate" name="enddate"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row form-group mt-2">
                                                            <div class="col col-md-3">
                                                                <label class="form-control-label">Description</label>
                                                            </div>
                                                            <div class="col-12 col-md-9">
                                                                <input type="text" name="description"
                                                                    placeholder="Description" class="form-control">
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button id="btn_closefinyearmodal" type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button id="btn_savefinyear" type="button" class="btn btn-primary">Save
                                                        FinYear</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <form class="form-horizontal">
                                        <div class="row form-group">
                                            <div class="col-lg-10 col-md-9 col-sm-9 col-xs-6">

                                                <input type="text" id="searchInput" class="form-control text-center"
                                                    style="::placeholder { color: red; }"
                                                    placeholder="Search by Grant No, Year or Status">
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
                                                @if (auth()->user()->haspermission('canaddoreditgrant'))
                                                    <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                                        data-bs-target="#addfinyearmodal">
                                                        Add Fin Year
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>

                                    <table id="finyearstable"
                                        class="table table-responsive table-bordered table-striped table-hover"
                                        style="margin:4px">
                                        <thead class="bg-secondary text-white">
                                            <tr>
                                                <th scope="col"># No</th>
                                                <th scope="col">Fin Year</th>
                                                <th scope="col">Start Date</th>
                                                <th scope="col">End Date</th>
                                                <th scope="col">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            @endauth
                        </div>
                        <script>
                            $(document).ready(function() {
                                document.getElementById('finyear_startdate')?.addEventListener('change', function(item) {
                                    calculateFinYear(item);
                                });
                                document.getElementById('finyear_enddate')?.addEventListener('change', function(item) {
                                    calculateFinYear(item);
                                });
                                document.getElementById('btn_savefinyear')?.addEventListener('click', function() {

                                    var formData = $('#form_addfinyear').serialize();

                                    // Function to fetch data using AJAX
                                    $.ajax({
                                        url: "{{ route('api.finyear.post') }}",
                                        type: 'POST',
                                        data: formData,
                                        dataType: 'json',
                                        success: function(response) {
                                            var closebtn = document.getElementById('btn_closefinyearmodal');
                                            if (closebtn) {
                                                closebtn.click();
                                            }
                                            showtoastmessage(response);
                                            fetchfinyearsData();
                                        },
                                        error: function(xhr, status, error) {
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
                                // Function to fetch data using AJAX
                                function fetchfinyearsData() {
                                    $.ajax({
                                        url: "{{ route('api.finyear.fetchallfinyears') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function(response) {
                                            populatefinyearsTable(response);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error fetching data:', error);
                                        }
                                    });
                                }

                                // Function to search data using AJAX
                                function searchData(searchTerm) {
                                    $.ajax({
                                        url: "{{ route('api.grants.fetchsearchgrants') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            search: searchTerm
                                        },
                                        success: function(response) {

                                            populateTable(response);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error searching data:', error);
                                        }
                                    });
                                }

                                // Function to populate table with data
                                function populatefinyearsTable(data) {
                                    var tbody = $('#finyearstable tbody');
                                    tbody.empty(); // Clear existing table rows
                                    if (data.length > 0) {
                                        $.each(data, function(index, data) {
                                            var row = '<tr>' +
                                                '<td>' + data.id + '</td>' +
                                                '<td>' + data.finyear + '</td>' +
                                                '<td>' + data.startdate + '</td>' +
                                                '<td>' + data.enddate + '</td>' +
                                                '<td>' + data.description + '</td>' +
                                                '</tr>';
                                            tbody.append(row);
                                        });
                                    } else {
                                        var row =
                                            '<tr><td colspan="5" class="text-center text-dark"><b>No Financial Years found</b></td></tr>';
                                        tbody.append(row);
                                    }
                                }

                                // Initial fetch when the page loads
                                fetchfinyearsData();

                                // Search input keyup event
                                $('#searchInput').on('keyup', function() {
                                    var searchTerm = $(this).val().toLowerCase();
                                    if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                                        searchData(searchTerm);
                                    } else if (searchTerm.length === 0) {
                                        fetchgrantsData(); // Fetch all data when search input is empty
                                    }
                                });


                                function calculateFinYear(item) {
                                    try {
                                        var savebtn = document.getElementById('btn_savefinyear');
                                        var startdate = document.getElementById('finyear_startdate').value;
                                        var enddate = document.getElementById('finyear_enddate').value;
                                        var enddate_el = document.getElementById('finyear_enddate');
                                        var startdate_el = document.getElementById('finyear_startdate');


                                        var StartDate = new Date(startdate);
                                        var EndDate = new Date(enddate);
                                        if (StartDate >= EndDate) {
                                            alert('The Start Date must not be greater or Equal to the Enddate!');
                                            savebtn?.setAttribute('disabled', true);
                                            item.target.value = null;
                                            return;
                                        } else {
                                            savebtn.removeAttribute('disabled');
                                            return;
                                        }
                                    } catch {

                                    }
                                }
                            });
                        </script>
                    </div>

                    <!-- settings tab -->
                    <div role="tabpanel" class="tab-pane" id="panel-settings">
                        <div>
                            <form id="form_currentgrant" class="form-horizontal">
                                @csrf
                                <div class="row form-group">
                                    <div class="col-3">
                                        <label>Current Open Grant</label>
                                    </div>
                                    <div class="col-7">
                                        <select type="text" id="current_grantno" name="current_grantno"
                                            class="form-control">
                                            <option value="">Select a Grant Item</option>
                                            @foreach ($allgrants as $grant)
                                                <option value="{{ $grant->grantid }}"
                                                    {{ isset($currentsettings) && $currentsettings['current_grant'] == $grant->grantid ? 'selected' : '' }}>
                                                    {{ $grant->title . ' - (' . $grant->financialyear->finyear . ')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button id="btn_savecurrentgrant" type="button" class="btn btn-info">Save</button>
                                    </div>
                                </div>
                            </form>
                            <form id="form_currentfinyear" class="form-horizontal">
                                @csrf
                                <div class="row form-group">
                                    <div class="col-3">
                                        <label>Current Fin Year</label>
                                    </div>
                                    <div class="col-7">
                                        <select type="text" id="current_fin_year" name="current_fin_year"
                                            class="form-control">
                                            <option value="">Select a Financial Year</option>
                                            @foreach ($finyears as $year)
                                                <option value="{{ $year->id }}"
                                                    {{ isset($currentsettings) && $currentsettings['current_year'] == $year->id ? 'selected' : '' }}>
                                                    {{ $year->id . ' -- (' . $year->finyear . ')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button id="btn_savecurrentfinyear" type="button" class="btn btn-info">Save</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <script>
                            $(document).ready(function() {

                                document.getElementById('btn_savecurrentgrant')?.addEventListener('click', function() {

                                    var formData = $('#form_currentgrant').serialize();

                                    // Function to fetch data using AJAX
                                    $.ajax({
                                        url: "{{ route('api.grants.settings.postcurrentgrant') }}",
                                        type: 'POST',
                                        data: formData,
                                        dataType: 'json',
                                        success: function(response) {
                                            showtoastmessage(response);
                                        },
                                        error: function(xhr, status, error) {
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

                                document.getElementById('btn_savecurrentfinyear')?.addEventListener('click', function() {

                                    var formData = $('#form_currentfinyear').serialize();

                                    // Function to fetch data using AJAX
                                    $.ajax({
                                        url: "{{ route('api.grants.settings.postcurrentfinyear') }}",
                                        type: 'POST',
                                        data: formData,
                                        dataType: 'json',
                                        success: function(response) {
                                            showtoastmessage(response);
                                        },
                                        error: function(xhr, status, error) {
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

                                // Function to fetch data using AJAX
                                function fetchallprojects() {
                                    $.ajax({
                                        url: "{{ route('api.projects.fetchallprojects') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function(response) {
                                            populateallProjects(response);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error fetching data:', error);
                                        }
                                    });
                                }

                                // Function to search data using AJAX
                                function searchallprojects(searchTerm) {
                                    $.ajax({
                                        url: "{{ route('api.projects.fetchsearchallprojects') }}",
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            search: searchTerm
                                        },
                                        success: function(response) {
                                            populateallProjects(response);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error searching data:', error);
                                        }
                                    });
                                }
                                var routeUrlTemplate =
                                    "{{ route('pages.supervision.monitoring.monitoringpage', ['id' => '__ID__']) }}";

                                // Function to populate table with data
                                function populateallProjects(data) {
                                    var canviewuser = false;
                                    var tbody = $('#allprojectstable tbody');
                                    tbody.empty(); // Clear existing table rows
                                    if (data.length > 0 && tbody) {
                                        $.each(data, function(index, data) {
                                            var projecturl = routeUrlTemplate.replace('__ID__', data.researchid);
                                            var row = '<tr>' +
                                                '<td><a class="nav-link pt-0 pb-0" href="' + projecturl + '">' + data
                                                .researchnumber + '</a></td>' +
                                                '<td>' + data.applicant?.name + '</td>' +
                                                '<td>' + data.projectstatus + '</td>' +
                                                '<td>' + Boolean(data.ispaused) + '</td>' +
                                                '<td>' + new Date(data.proposal?.commencingdate).toDateString("en-US") +
                                                '</td>' +
                                                '<td>' + new Date(data.proposal?.terminationdate).toDateString("en-US") +
                                                '</td>' +
                                                '</tr>';
                                            tbody.append(row);
                                        });
                                    } else {
                                        var row = '<tr><td colspan="5">No Active Projects found</td></tr>';
                                        tbody.append(row);
                                    }
                                }

                                // Search input keyup event
                                $('#searchInput').on('keyup', function() {
                                    var searchTerm = $(this).val().toLowerCase();
                                    if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                                        searchallprojects(searchTerm);
                                    } else if (searchTerm.length === 0) {
                                        fetchallprojects(); // Fetch all data when search input is empty
                                    }
                                });

                                // Initial fetch when the page loads
                                fetchallprojects();
                            });
                        </script>


                    </div>

                </div>
            </div>


        </div>
    @endauth
@endsection
