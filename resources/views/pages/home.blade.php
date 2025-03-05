@extends('layouts.master')

@section('content')
    <div class="row bg-light" style="padding-right:0px">
        @auth
            <div class="row">

                <!-- Remaining rows for proposals and projects will go here -->
                <div class="col-12">
                    <style>
                        .dashboard {
                            display: grid;
                            grid-template-columns: 1fr;
                            gap: 20px;
                            margin-right: 20px;
                            margin-left: 20px;
                        }

                        .welcome-card {
                            background-color: #fff;
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            text-align: center;
                        }

                        .full-width-card {
                            background-color: #f8f9fa;
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            text-align: center;
                        }

                        .full-width-card h3 {
                            margin: 0;
                            font-size: 24px;
                            margin-bottom: 10px;
                        }

                        .full-width-card p {
                            margin: 0;
                            font-size: 18px;
                            color: #555;
                        }

                        .card-grid {
                            display: grid;
                            grid-template-columns: repeat(4, 1fr);
                            gap: 20px;
                        }

                        .card {
                            background-color: #f8f9fa;
                            /* bg-light color */
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            text-align: center;
                        }

                        .card h3 {
                            margin: 0;
                            font-size: 16px;
                            margin-bottom: 10px;
                        }

                        .card p {
                            margin: 0;
                            font-size: 18px;
                            color: #555;
                        }

                        .card .icon {
                            font-size: 36px;
                            margin-bottom: 10px;
                            color: #2196F3;
                        }

                        @media (max-width: 576px) {
                            .card-grid {
                                grid-template-columns: 1fr;
                            }
                        }
                    </style>

                    <!-- Existing code for Proposals and Projects rows -->

                    <div class="dashboard">

                        <!-- first Row: welcome -->
                        <div class="full-width-card">
                            <div>
                                <h3>Home. My Analysis</h3>
                                <!-- <p>{{ Auth()->user()->name }}</p> -->
                            </div>
                        </div>


                        <!-- Second Row: Projects -->
                        <div class="card-grid">
                            <div class="card total">
                                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                                <h3>{{ number_format($totalAmountReceived, 2) }}</h3>
                                <p>Total Fundings</p>
                            </div>
                            <div class="card projects-active">
                                <div class="icon"><i class="fas fa-play-circle"></i></div>
                                <h3>{{ $activeprojects }}</h3>
                                <p>Active Projects</p>
                            </div>
                            <div class="card projects-canceled">
                                <div class="icon"><i class="fas fa-ban"></i></div>
                                <h3>{{ $cancelledprojects }}</h3>
                                <p>Canceled Projects</p>
                            </div>
                            <div class="card projects-completed">
                                <div class="icon"><i class="fas fa-check"></i></div>
                                <h3>{{ $completedprojects }}</h3>
                                <p>Completed Projects</p>
                            </div>
                        </div>

                        <!-- Third Row: Proposals -->
                        <div class="card-grid">
                            <div class="card total">
                                <div class="icon"><i class="fas fa-file-alt"></i></div>
                                <h3>{{ $totalProposals }}</h3>
                                <p>Total Proposals</p>
                            </div>
                            <div class="card pending">
                                <div class="icon"><i class="fas fa-clock"></i></div>
                                <h3>{{ $pendingProposals }}</h3>
                                <p>Pending Proposals</p>
                            </div>
                            <div class="card rejected">
                                <div class="icon"><i class="fas fa-times-circle"></i></div>
                                <h3>{{ $rejectedProposals }}</h3>
                                <p>Rejected Proposals</p>
                            </div>
                            <div class="card approved">
                                <div class="icon"><i class="fas fa-check-circle"></i></div>
                                <h3>{{ $approvedProposals }}</h3>
                                <p>Approved Proposals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>
@endsection
