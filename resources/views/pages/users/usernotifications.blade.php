@extends('layouts.master')

@section('content')
@if (isset($notificationtype))
    <div class="row">
        <style>
            #searchInput::placeholder {
                color: #cdc8c8;
                /* Change to your desired color */
            }
        </style>

        <div class="row form-group" style="padding-top:4px">
            @if (auth()->user()->haspermission('canviewallusers'))
                <form class="form-horizontal">
                    <div class="row form-group">
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-6">
                            <input type="text" id="searchInput" class="form-control text-center"
                                placeholder="Search by Name or PFNO">
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                            @if (auth()->user()->haspermission('canaddorremovenotifiableuser'))
                                <button type="button" class="btn btn-info text-white ml-0 mr-0 pl-0 pr-0" data-bs-toggle="modal"
                                    data-bs-target="#addnotifiableusermodal">
                                    New Notifiable User
                                </button>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Add Notifiable Users Modal -->
                <div class="modal fade" id="addnotifiableusermodal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="pauseprojectmodalLabel" aria-hidden="true">
                    <form>
                        @csrf
                    </form>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="pauseprojectmodalLabel">Pause Project</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table id="alluserstonotifytable"
                                    class="table table-responsive table-bordered table-striped table-hover" style="margin:4px">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th scope="col">Select</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">PFNO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($nonNotifiableUsers as $nuser)
                                            <tr>
                                                <td>
                                                    <input id="{{ $nuser->userid }}" class="form-check-input"
                                                        value="{{ $nuser->userid }}" type="checkbox">
                                                </td>
                                                <td>
                                                    <label for="{{$nuser->userid}}"
                                                        class="form-check-label">{{$nuser->name }}</label>
                                                </td>
                                                <td>
                                                    <label for="{{$nuser->userid}}"
                                                        class="form-check-label">{{$nuser->pfno}}</label>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button id="btn_closeaddnotifiableusermodal" type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button id="btn_savenotifiableusers" type="button" class="btn btn-primary">Add Users</button>
                            </div>
                        </div>
                    </div>
                </div>

                <table id="notifiableuserstable" class="table table-responsive table-bordered table-striped table-hover"
                    style="margin:4px">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">PFNO</th>
                            <th scope="col">Role</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated dynamically -->
                    </tbody>
                </table>
            @endif
        </div>

        <script>
            function removenotiableuser(type_id, userid) {
                const removeusersurl = `{{ route('api.notificationtype.removenotifiableuser', ['id' => ':id']) }}`.replace(':id', type_id);
                var csrfToken = $('input[name="_token"]').val();
                $.ajax({
                    url: removeusersurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        users: [userid],
                        _token: csrfToken
                    },
                    success: function (response) {
                        fetchData(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error removing user:', error);
                    }
                });
            }

            $(document).ready(function () {

                let typeid = "{{ isset($notificationtype) ? $notificationtype->typeuuid : '' }}";
                const typeurl = `{{ route('api.notificationtype.fetchtypewiseusers', ['id' => ':id']) }}`.replace(':id', typeid);
                const addusersurl = `{{ route('api.notificationtype.addnotifiableusers', ['id' => ':id']) }}`.replace(':id', typeid);

                $('#btn_savenotifiableusers').on('click', function () {
                    var checkedCheckboxes = $('#alluserstonotifytable input[type="checkbox"]:checked');
                    var users_ids = Array.from(checkedCheckboxes).map(function (checkbox) {
                        return checkbox.id;
                    });
                    var perm = { 'users': users_ids }
                    if (perm.users.length <= 0) {
                        showtoastmessage({ 'message': "You must select at least one User!", 'type': "warning" });
                        return;
                    }
                    var csrfToken = $('input[name="_token"]').val();
                    perm['_token'] = csrfToken;
                    $.ajax({
                        url: addusersurl,
                        type: 'POST',
                        dataType: 'json',
                        data: perm,
                        success: function (response) {
                            $('#btn_closeaddnotifiableusermodal').click();
                            showtoastmessage(response);
                            fetchData();
                        },
                        error: function (xhr, status, error) {
                            showtoastmessage({ 'message': 'Error Occurred!', 'type': 'danger' })
                            console.error('Error saving users:', error);
                        }
                    });

                });

                window.fetchData = function () {
                    $.ajax({
                        url: typeurl,
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            populatenotifiableusersTable(response);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching data:', error);
                        }
                    });
                }

                function searchData(searchTerm) {
                    $.ajax({
                        url: typeurl,
                        type: 'GET',
                        dataType: 'json',
                        data: { search: searchTerm },
                        success: function (response) {
                            populatenotifiableusersTable(response);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error searching data:', error);
                        }
                    });
                }

                function populatenotifiableusersTable(data) {
                    let not_type = "{{$notificationtype->typeuuid}}";
                    var tbody = $('#notifiableuserstable tbody');
                    tbody.empty();
                    if (data.length > 0) {
                        $.each(data, function (index, data) {
                            var row = '<tr>' +
                                '<td>' + data.applicant?.name + '</td>' +
                                '<td>' + data.applicant?.pfno + '</td>' +
                                '<td>' + getrolename(data.applicant?.role) + '</td>' +
                                '<td>' +
                                '<button class="btn btn-sm btn-danger" onclick="removenotiableuser(' + "'" + not_type + "'" + ',' + "'" + data.applicant?.userid + "'" + ')">' +
                                '<i class="bi bi-trash"></i> Delete' +
                                '</button>' +
                                '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                    }
                    else {
                        var row = '<tr><td colspan="4">No Users found</td></tr>';
                        tbody.append(row);
                    }
                }

                function getrolename(roleid) {
                    switch (roleid) {
                        case 1: return 'Committee';
                        case 2: return 'Researcher';
                        case 3: return 'Guest';
                        default: return 'Unknown';
                    }
                }

                fetchData();

                $('#searchInput').on('keyup', function () {
                    var searchTerm = $(this).val().toLowerCase();
                    if (searchTerm.length >= 3) {
                        searchData(searchTerm);
                    } else if (searchTerm.length === 0) {
                        fetchData();
                    }
                });

            });
        </script>
    </div>
@endif
@endsection