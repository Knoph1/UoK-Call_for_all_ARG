@extends('layouts.master')

@section('content')
<div class="row">


    @auth
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
                    <button class="nav-link active" id="nav-basicdetails-tab" data-bs-toggle="tab"
                        data-bs-target="#panel-basicdetails" type="button" role="tab" aria-controls="panel-basicdetails"
                        aria-selected="true">Basic Details</button>
                    <button class="nav-link" id="nav-actions-tab" data-bs-toggle="tab" data-bs-target="#panel-actions"
                        type="button" role="tab" aria-controls="panel-actions" aria-selected="false">Actions</button>
                    <button class="nav-link" id="nav-rights-tab" data-bs-toggle="tab" data-bs-target="#panel-rights"
                        type="button" role="tab" aria-controls="panel-rights" aria-selected="false">Rights</button>



                </div>
            </nav>

            <!-- Tab panes -->
            <div class="tab-content prop-tabpanel">

                <!-- basic details Details -->
                <div role="tabpanel" class="tab-pane active" id="panel-basicdetails">
                    <!-- basic Details Form -->
                    <form method="POST" id="form_basicdetails" enctype="multipart/form-data" class="form-horizontal">
                        @csrf
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Full Name</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="fullname" name="fullname" placeholder="Your Full Name"
                                    value="{{ $user->name }}" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Email</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="email" name="email" placeholder="Your Email"
                                    value="{{ $user->email }}" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">PF Number</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="pfno" name="pfno" placeholder="Your PF Number"
                                    value="{{ $user->pfno }}" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Phone Number</label>

                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="phonenumber" name="phonenumber" placeholder="Your Phone Number"
                                    value="{{ $user->phonenumber }}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">Registration Date</label>

                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" placeholder="User Registration Date" value="{{ $user->created_at }}"
                                    class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">User Role</label>

                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" placeholder="Your Role" value="{{ $user->role }}" class="form-control"
                                    readonly>
                            </div>
                        </div>
                        @if (Auth::user()->haspermission('canedituserprofile'))
                            <div class="row form-group">
                                <div class="col text-center">
                                    <button id="btn_editprofile" type="button" class="btn btn-info">Edit Profile</button>

                                    <button id="btn_updateprofile" type="button" class="btn btn-success" disabled
                                        hidden>Update</button>
                                </div>
                            </div>
                        @endif
                    </form>

                    <script>
                        $(document).ready(function () {

                            let userid = "{{ isset($user) ? $user->userid : '' }}"; // Check if user is set
                            // Assuming prop is passed to the Blade view from the Laravel controller
                            const userurl = `{{ route('api.users.updatebasicdetails', ['id' => ':id']) }}`.replace(':id', userid);
                            document.getElementById('btn_editprofile')?.addEventListener('click', function () {

                                document.getElementById('fullname').removeAttribute('readonly');
                                document.getElementById('email').removeAttribute('readonly');
                                document.getElementById('phonenumber').removeAttribute('readonly');
                                document.getElementById('pfno').removeAttribute('readonly');
                                document.getElementById('btn_updateprofile').removeAttribute('hidden');
                                document.getElementById('btn_updateprofile').removeAttribute('disabled');
                                this.disabled = true;
                                this.hidden = true;
                            });
                            document.getElementById('btn_updateprofile')?.addEventListener('click', function () {

                                var formData = $('#form_basicdetails').serialize();
                                // var csrfToken = document.getElementsByName('_token')[0].value;
                                //     var formdata={'_token': csrfToken};
                                // Function to fetch data using AJAX
                                $.ajax({
                                    url: userurl,
                                    type: 'POST',
                                    data: formData,
                                    dataType: 'json',
                                    success: function (response) {
                                        showtoastmessage(response);
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
                        });
                    </script>
                </div>


                <!-- Actions tab -->
                <div role="tabpanel" class="tab-pane" id="panel-actions">
                    <form method="POST" id="changepasswordform" enctype="multipart/form-data" class="form-horizontal">
                        @csrf

                        <div class="row form-group">

                            <div class="text-center">
                                <button id="btn_resetpassword" type="button" class="btn btn-info">Reset User
                                    Password</button>
                            </div>
                        </div>

                    </form>

                    <hr>
                    <div class="row form-group">
                        <form id="form_userrole" method="POST">
                            @csrf
                            <div class=" form-group col-12">
                                <div class=" form-check">
                                    <input id="isadmin" name="isadmin" class="form-check-input" type="checkbox"
                                        {{($user->isadmin) ? 'checked' : ''}}>
                                    <label for="isadmin" class="form-check-label">This User is Super Admin</i>)</label>
                                </div>
                            </div>
                            <div class="row form-group ">
                                <div class="col-3">
                                    <label>Change User Role</label>
                                </div>
                                <div class="col-9">

                                    <select id="userrole" name="userrole" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1" {{ (isset($user) && $user->role == "1") ? 'selected' : '' }}>
                                            Committee</option>
                                        <option value="2" {{ (isset($user) && $user->role == "2") ? 'selected' : '' }}>
                                            Researcher</option>
                                        <option value="3" {{ (isset($user) && $user->role == "3") ? 'selected' : '' }}>
                                            Guest</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-9">
                                <div class=" form-check">
                                    <input id="userisactive" name="userisactive" class="form-check-input" type="checkbox"
                                        {{($user->isactive) ? 'checked' : ''}}>
                                    <label for="userisactive" class="form-check-label">User is Active (<i>Can
                                            login!</i>)</label>

                                    <br />

                                </div>
                            </div>
                            <div class="text-center">
                                <button id="btn_updaterole" type="button" class="btn btn-info">Update User Role</button>
                            </div>
                        </form>
                    </div>
                    <script>
                        $(document).ready(function () {
                            var roleselector = document.getElementById('userrole');
                            var isadmincheckbox = document.getElementById('isadmin');
                            isadmincheckbox.addEventListener('change', function () {
                                if (roleselector) {
                                    if (isadmincheckbox.checked) {
                                        roleselector.disabled = true;
                                        isadmincheckbox
                                    }
                                    else {
                                        roleselector.disabled = false;
                                    }
                                }
                            });

                            let userid = "{{ isset($user) ? $user->userid : '' }}"; // Check if user is set
                            const passwordchangeurl = `{{ route('password.requestreset') }}`;
                            const updateroleurl = `{{ route('api.users.updaterole', ['id' => ':id']) }}`.replace(':id', userid.toString());

                            document.getElementById('btn_resetpassword')?.addEventListener('click', function () {
                                var csrfToken = document.getElementsByName('_token')[0].value;
                                var formdata = { '_token': csrfToken };
                                formdata['email']="{{$user->email}}";
                                $.ajax({
                                    url: passwordchangeurl,
                                    type: 'POST',
                                    data: formdata,
                                    dataType: 'json',
                                    success: function (response) {
                                        showtoastmessage(response);
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
                            document.getElementById('btn_updaterole')?.addEventListener('click', function () {

                                var formData = $('#form_userrole').serialize();

                                // Function to fetch data using AJAX
                                $.ajax({
                                    url: updateroleurl,
                                    type: 'POST',
                                    data: formData,
                                    dataType: 'json',
                                    success: function (response) {
                                        showtoastmessage(response);
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

                        });
                    </script>
                </div>
                <!-- Rights tab -->
                <div role="tabpanel" class="tab-pane" id="panel-rights">
                    <div>
                        @if (!(isset($user) && $user->issuperadmin()))
                            <h5 class="text-center">Select permissions preffered for this user only!</h5>
                            <div id="permissions_list_div" class="container mt-1">
                                @if ($user->role == '1' || $user->role == '3')
                                    <div class="card">
                                        <div class="card-header">
                                            Admin Rights
                                        </div>
                                        <div class="card-body">
                                            @if (isset($permissions))
                                                <table class="table table-responsive table-bordered table-hover">
                                                    <thead class="bg-secondary text-white">
                                                        <td>Value</td>
                                                        <td>Menu Name</td>
                                                        <td>Role</td>
                                                        <td>Description</td>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($permissions->where('targetrole', 1)->toQuery()->orderBy('permissionlevel')->orderBy('priorityno')->get() as $perm)
                                                            <tr>
                                                                <td>
                                                                    <input id="{{ $perm->pid }}" class="form-check-input"
                                                                        value="{{ $perm->pid }}" type="checkbox" @if(isset($user) && $user->hasPermission($perm->shortname)) checked @endif>
                                                                </td>
                                                                <td>
                                                                    <label for="{{$perm->pid}}"
                                                                        class="form-check-label">{{$perm->menuname }}</label>
                                                                </td>
                                                                <td>
                                                                    <label for="{{$perm->pid}}"
                                                                        class="form-check-label">{{$perm->targetrole == 1 ? 'Admin' : 'Non Admin'}}</label>
                                                                </td>
                                                                <td>
                                                                    <label for="{{$perm->pid}}"
                                                                        class="form-check-label">{{$perm->description}}</label>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                @endif                          <div class="row col-12 form-group">

                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        Non Admin Rights
                                    </div>
                                    <div class="card-body">
                                        @if (isset($permissions))
                                            <table class="table table-responsive table-bordered table-hover">
                                                <thead class="bg-secondary text-white">
                                                    <td>Value</td>
                                                    <td>Menu Name</td>
                                                    <td>Role</td>
                                                    <td>Description</td>
                                                </thead>
                                                <tbody>
                                                    @foreach ($permissions->where('targetrole', 2) as $perm)
                                                        <tr>
                                                            <td>
                                                                <input id="{{ $perm->pid }}" class="form-check-input"
                                                                    value="{{ $perm->pid }}" type="checkbox" @if(isset($user) && $user->hasPermission($perm->shortname)) checked @endif>
                                                            </td>
                                                            <td>
                                                                <label for="{{$perm->pid}}"
                                                                    class="form-check-label">{{$perm->menuname }}</label>
                                                            </td>
                                                            <td>
                                                                <label for="{{$perm->pid}}"
                                                                    class="form-check-label">{{$perm->targetrole == 1 ? 'Admin' : 'Non Admin'}}</label>
                                                            </td>
                                                            <td>
                                                                <label for="{{$perm->pid}}"
                                                                    class="form-check-label">{{$perm->description}}</label>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col text-center">
                                        <button id="btn_save_permissions" type="button" class="btn btn-primary">Save
                                            Permissions</button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center">
                                <h5>This is a <i>superuser (Admin)</i> and is Limited to Some Rights!</h5>
                            </div>
                        @endif
                    </div>
                    @php
                        // Ensure $user is properly encoded to JSON
                        $userJson = json_encode($user);
                    @endphp
                    <script>
                        $(document).ready(function () {
                            var user = {!! $userJson !!};
                            var updateUrlTemplate = "{{ route('api.users.updatepermissions', ['id' => $user->userid]) }}";

                            document.getElementById('btn_save_permissions')?.addEventListener('click', function () {
                                var checkedCheckboxes = document.getElementById('permissions_list_div').querySelectorAll('div input[type="checkbox"]:checked');
                                checkedCheckboxes = Array.from(checkedCheckboxes);
                                var permissions = Array.from(checkedCheckboxes).map(function (checkbox) {
                                    return checkbox.id;
                                });
                                var perm = { 'permissions': permissions }
                                if (perm.permissions.length <= 0) {
                                    showtoastmessage({ 'message': "You must select atleast one Permission!", 'type': "warning" });
                                    return;
                                }
                                var csrfToken = document.getElementsByName('_token')[0].value;
                                perm['_token'] = csrfToken;
                                $.ajax({
                                    url: updateUrlTemplate,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: perm,
                                    success: function (response) {
                                        showtoastmessage(response);
                                    },
                                    error: function (xhr, status, error) {
                                        showtoastmessage({ 'message': 'Error Occured!', 'type': 'danger' })
                                        console.error('Error fetching data:', error);
                                    }
                                });

                            });
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
                                var tbody = $('#proposalstable tbody');
                                tbody.empty(); // Clear existing table rows
                                if (data.length > 0) {
                                    $.each(data, function (index, data) {
                                        var userurl = routeUrlTemplate.replace('__ID__', data.userid);
                                        var row = '<tr>' +
                                            '<td>' + data.name + '</td>' +
                                            '<td><a class="nav-link pt-0 pb-0" href="' + userurl + '">' + data.email + '</a></td>' +
                                            '<td>' + data.pfno + '</td>' +
                                            '<td>' + Boolean(data.isactive) + '</td>' +
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


            </div>
        </div>
    @endauth
</div>
<script>
    $(document).ready(function () {


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
            var tbody = $('#proposalstable tbody');
            tbody.empty(); // Clear existing table rows
            if (data.length > 0) {
                $.each(data, function (index, data) {
                    var userurl = routeUrlTemplate.replace('__ID__', data.userid);
                    var row = '<tr>' +
                        '<td>' + data.name + '</td>' +
                        '<td><a class="nav-link pt-0 pb-0" href="' + userurl + '">' + data.email + '</a></td>' +
                        '<td>' + data.pfno + '</td>' +
                        '<td>' + Boolean(data.isactive) + '</td>' +
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
@endsection