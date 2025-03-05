@extends('layouts.master')

@section('content')
    <div class="row bg-light" style="padding-right:0px">
        @auth
            @if (Auth::check() && Auth::user()->haspermission('canviewadmindashboard'))
                <div class="row text-center " style="padding-top:8px">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card text-white  bg-secondary  mb-3 " style="max-width: 18rem;">
                            <div class="card-header">Total Proposals</div>
                            <div class="card-body">
                                <h1 class="card-title">{{ isset($allProposalscount) ? $allProposalscount : 0 }}</h1>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card text-white bg-success mb-3  " style="max-width: 18rem;">
                            <div class="card-header">Approved</div>
                            <div class="card-body">
                                <h1 class="card-title">{{ isset($approvedProposalsCount) ? $approvedProposalsCount : 0 }}</h1>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

                        <div class="card text-white bg-info mb-3 " style="max-width: 18rem;">
                            <div class="card-header ">Pending</div>
                            <div class="card-body">
                                <h1 class="card-title">{{ isset($pendingProposalsCount) ? $pendingProposalsCount : 0 }}</h1>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

                        <div class="card text-white bg-danger mb-3 " style="max-width: 18rem;">
                            <div class="card-header ">Rejected</div>
                            <div class="card-body">
                                <h1 class="card-title">{{ isset($rejectedProposalsCount) ? $rejectedProposalsCount : 0 }}</h1>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row text-center ">
                    <div class="text-center text-dark">
                        <br />
                        <h5><b>Applications Count by Themes</b></h5>
                    </div>
                    <div class="chartcard">
                        <!-- <p class="text-center mt-2"><u><b>Theme Analysis Chart (Gender and Proposal Status)</b></u></p> -->
                        <div class="col-xs-12 " style="width: 99%;height: 300px;margin:8px">
                            <canvas id="dashboardchart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>

                </div>
            @endif

        @endauth
    </div>
    <script>
        $(document).ready(function() {

            // Function to search data using AJAX
            function fetchData(themefilter, departmentfilter) {

                $.ajax({
                    url: "{{ route('api.dashboard.chartdata') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        populatedashboardChart(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error searching data:', error);
                    }
                });
            }

            // Function to populate the chart
            function populatedashboardChart(data) {
                const canvas = document.getElementById('dashboardchart');
                const ctx = canvas.getContext('2d');

                // Destroy existing chart instance if it exists
                if (Chart.getChart(canvas)) {
                    Chart.getChart(canvas).destroy();
                }

                new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            decimation: {
                                enabled: false,
                                algorithm: 'min-max',
                            },
                        },
                    }
                });
            }

            // Initial fetch when the page loads
            fetchData(null, null);

        });
    </script>
@endsection
