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
                        <button class="nav-link active" id="nav-myprofile-tab" data-bs-toggle="tab"
                            data-bs-target="#panel-myprofile" type="button" role="tab" aria-controls="panel-myprofile"
                            aria-selected="true">Basic Details</button>
                        <button class="nav-link" id="nav-security-tab" data-bs-toggle="tab" data-bs-target="#panel-security"
                            type="button" role="tab" aria-controls="panel-security" aria-selected="false">Security</button>
                    </div>
                </nav>

                <!-- Tab panes -->
                <div class="tab-content prop-tabpanel">

                    <!-- myprofile Details -->
                    <div role="tabpanel" class="tab-pane active" id="panel-myprofile">
                        <!-- Personal Details Form -->
                        <form method="POST" id="form_basicdetails" enctype="multipart/form-data" class="form-horizontal">
                            @csrf
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label">Full Name</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="fullname" name="fullname" placeholder="Your Full Name"
                                        value="{{ Auth::user()->name }}" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label">Email</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="email" name="email" placeholder="Your Email"
                                        value="{{ Auth::user()->email }}" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label">PF Number</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="pfno" name="pfno" placeholder="Your PF Number"
                                        value="{{ Auth::user()->pfno }}" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label">Phone Number</label>

                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="phonenumber" name="phonenumber" placeholder="Your Phone Number"
                                        value="{{ Auth::user()->phonenumber }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label">Registration Date</label>

                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" placeholder="Your Registration Date"
                                        value="{{ Auth::user()->created_at }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col text-center">
                                    <button id="btn_editprofile" type="button" class="btn btn-info">Edit Profile</button>

                                    <button id="btn_updateprofile" type="button" class="btn btn-success" disabled
                                        hidden>Update</button>
                                </div>
                            </div>

                        </form>

                        <script>
                            $(document).ready(function() {
                                let userid = "{{ Auth::user()->userid }}"
                                // Assuming prop is passed to the Blade view from the Laravel controller
                                const basicupdateurl = `{{ route('api.users.updatebasicdetails', ['id' => ':id']) }}`.replace(':id',
                                    userid);
                                const punlicationsurl = `{{ route('api.proposals.fetchpublications', ['id' => ':id']) }}`.replace(':id',
                                    userid);
                                document.getElementById('btn_editprofile')?.addEventListener('click', function() {

                                    document.getElementById('fullname').removeAttribute('readonly');
                                    document.getElementById('email').removeAttribute('readonly');
                                    document.getElementById('phonenumber').removeAttribute('readonly');
                                    document.getElementById('pfno').removeAttribute('readonly');
                                    document.getElementById('btn_updateprofile').removeAttribute('hidden');
                                    document.getElementById('btn_updateprofile').removeAttribute('disabled');
                                    this.disabled = true;
                                    this.hidden = true;
                                });
                                document.getElementById('btn_updateprofile')?.addEventListener('click', function() {

                                    var formData = $('#form_basicdetails').serialize();

                                    // Function to fetch data using AJAX
                                    $.ajax({
                                        url: basicupdateurl,
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
                            });
                        </script>
                    </div>


                    <!-- security tab -->
                    <div role="tabpanel" class="tab-pane" id="panel-security">
                        <form method="POST" enctype="multipart/form-data" class="form-horizontal">
                            @csrf
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label">Current Password</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="password" id="currentpass" name="currentpass" placeholder="Current Password"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label">New Password</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="password" id="newpass" name="newpass" placeholder="New Password"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label">Confirm Password</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="password" id="confirmpass" name="confirmpass"
                                        placeholder="Confirm Password" class="form-control">
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class="form-control-label"></label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <div class=" form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label" for="flexCheckDefault">By changing your password you
                                            will automatically be Signed Out!</label>

                                        <br />

                                    </div>
                                </div>
                                <div class="text-center">
                                    <button id="btn_changepassword" class="btn btn-warning">Change Password</button>
                                </div>
                            </div>

                        </form>


                    </div>

                </div>
            </div>
        </div>
    @endauth
@endsection
