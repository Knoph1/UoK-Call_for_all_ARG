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

            #searchInput::placeholder {
                color: #cdc8c8;
                /* Change to your desired color */
            }
        </style>

        <div class="prop-tabcontainer">
            <!-- Nav tabs -->
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-manageusers-tab" data-bs-toggle="tab"
                        data-bs-target="#panel-manageusers" type="button" role="tab" aria-controls="panel-manageusers"
                        aria-selected="true">Users</button>
                    <button class="nav-link " id="nav-notifications-tab" data-bs-toggle="tab"
                        data-bs-target="#panel-notifications" type="button" role="tab" aria-controls="panel-notifications"
                        aria-selected="true">Notificatios</button>
                </div>
            </nav>

            <!-- Tab panes -->
            <div class="tab-content prop-tabpanel">

                <!-- manageusers Details -->
                <div role="tabpanel" class="tab-pane active" id="panel-manageusers">
                    <div class="row">


                        <div class="row form-group" style="padding-top:4px">
                            @if (auth()->user()->haspermission('canviewallusers'))
                                <form class="form-horizontal">
                                    <div class="row form-group">
                                        <div class="col-12">

                                            <input type="text" id="searchInput" class="form-control text-center"
                                                style="::placeholder { color: red; }"
                                                placeholder="Search by User Name, Email, PFNO or Is Active Status">
                                        </div>
                                    </div>
                                </form>

                                <table id="userstable" class="table table-responsive table-bordered table-striped table-hover"
                                    style="margin:4px">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">PFNO</th>
                                            <th scope="col">Can Login</th>
                                            <th scope="col">Role</th>
                                            <th scope="col">Date Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                    <script>

                        $(document).ready(function () {
                            var canviewuser = false;
                            @if(Auth::user()->haspermission('canedituserprofile'))
                                canviewuser = true;
                            @endif

                            // Function to fetch data using AJAX
                            function fetchData() {
                                $.ajax({
                                    url: "{{ route('api.users.fetchallusers') }}",
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        populateTable(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error fetching data:', error);
                                    }
                                });
                            }

                            // Function to search data using AJAX
                            function searchData(searchTerm) {
                                $.ajax({
                                    url: "{{ route('api.users.fetchsearchusers') }}",
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        search: searchTerm
                                    },
                                    success: function (response) {

                                        populateTable(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error searching data:', error);
                                    }
                                });
                            }
                            var routeUrlTemplate = "{{ route('pages.users.viewsingleuser', ['id' => '__ID__']) }}";

                            // Function to populate table with data
                            function populateTable(data) {
                                var tbody = $('#userstable tbody');
                                tbody.empty(); // Clear existing table rows
                                if (data.length > 0) {
                                    $.each(data, function (index, data) {
                                        var userurl = routeUrlTemplate.replace('__ID__', data.userid);
                                        var row = '<tr>' +
                                            '<td>' + data.name + '</td>' +
                                            (canviewuser ? '<td><a class="nav-link pt-0 pb-0" href="' + userurl + '">' + data.email + '</a></td>' : '<td>' + data.email + '</td>') +
                                            '<td>' + data.pfno + '</td>' +
                                            '<td>' + Boolean(data.isactive) + '</td>' +
                                            '<td>' + getrolename(data.role) + '</td>' +
                                            '<td>' + new Date(data.created_at).toDateString("en-US") + '</td>' +
                                            '</tr>';
                                        tbody.append(row);
                                    });
                                }
                                else {
                                    var row = '<tr><td colspan="5">No Users found</td></tr>';
                                    tbody.append(row);
                                }
                            }
                            function getrolename(roleid) {
                                if (roleid == 1) {
                                    return 'Committee';
                                }
                                else if (roleid == 2) {
                                    return 'Researcher';
                                }
                                else if (roleid == 3) {
                                    return 'Guest';
                                }
                                else {
                                    return 'unknown';
                                }
                            }
                            // Initial fetch when the page loads
                            fetchData();

                            // Search input keyup event
                            $('#searchInput').on('keyup', function () {
                                var searchTerm = $(this).val().toLowerCase();
                                if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                                    searchData(searchTerm);
                                } else if (searchTerm.length === 0) {
                                    fetchData(); // Fetch all data when search input is empty
                                }
                            });
                        });
                    </script>
                </div>

                <!-- notifications Details -->
                <div role="tabpanel" class="tab-pane" id="panel-notifications">
                    <div class="row">

                        <div class="row form-group" style="padding-top:4px">

                            <form class="form-horizontal">
                                <div class="row form-group">
                                    <div class="col-12">
                                        <input type="text" id="searchInput" class="form-control text-center"
                                            style="::placeholder { color: red; }"
                                            placeholder="Search by User Name, Email, PFNO or Is Active Status">
                                    </div>
                                </div>
                            </form>

                            <table id="notificationtypestable"
                                class="table table-responsive table-bordered table-striped table-hover" style="margin:4px">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th scope="col">Type</th>
                                        <th scope="col">Notify Owner</th>
                                        <th scope="col">Description</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function () {

                            // Function to fetch data using AJAX
                            function fetchnotificationtypesData() {
                                $.ajax({
                                    url: "{{ route('api.notificationtype.fetchallnotificationtypes') }}",
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        populatenotificationsTable(response);
                                    },
                                    error: function (xhr, status, error) {
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
                                    success: function (response) {

                                        populateTable(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error searching data:', error);
                                    }
                                });
                            }

                            // Function to populate table with data
                            function populatenotificationsTable(data) {
                                var tbody = $('#notificationtypestable tbody');
                                tbody.empty(); // Clear existing table rows

                                var routeUrlTemplate = "{{ route('pages.notificationtype.managenotificationtype', ['id' => '__ID__']) }}";

                                if (data.length > 0) {
                                    $.each(data, function (index, data) {
                                        var typeurl = routeUrlTemplate.replace('__ID__', data.typeuuid);
                                        var row = '<tr>' +
                                            '<td><a class="nav-link pt-0 pb-0" href="' + typeurl + '">' + data.typename + '</a></td>' +
                                            '<td>' + Boolean(data.notifyowner) + '</td>' +
                                            '<td>' + data.description ?? '' + '</td>' +
                                            '</tr>';
                                        tbody.append(row);
                                    });
                                }
                                else {
                                    var row = '<tr><td colspan="2" class="text-center text-dark"><b>No Notification Types found</b></td></tr>';
                                    tbody.append(row);
                                }
                            }

                            // Initial fetch when the page loads
                            fetchnotificationtypesData();

                            // Search input keyup event
                            $('#searchInput').on('keyup', function () {
                                var searchTerm = $(this).val().toLowerCase();
                                if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
                                    searchData(searchTerm);
                                } else if (searchTerm.length === 0) {
                                    fetchnotificationtypesData(); // Fetch all data when search input is empty
                                }
                            });

                        });
                    </script>
                </div>


            </div>
        </div>


    </div>
@endauth
@endsection