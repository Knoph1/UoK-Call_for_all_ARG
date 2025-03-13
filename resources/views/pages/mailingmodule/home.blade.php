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
      <button class="nav-link active" id="nav-pendingmails-tab" data-bs-toggle="tab"
        data-bs-target="#panel-pendingmails" type="button" role="tab" aria-controls="panel-pendingmails"
        aria-selected="true">Pending Mails</button>
      <button class="nav-link" id="nav-failedmails-tab" data-bs-toggle="tab" data-bs-target="#panel-failedmails"
        type="button" role="tab" aria-controls="panel-failedmails" aria-selected="false">Failed Mails</button>
      </div>
    </nav>

    <!-- Tab panes -->
    <div class="tab-content prop-tabpanel">

      <div role="tabpanel" class="tab-pane active" id="panel-pendingmails">
      <div class="row">
        <div class="row form-group" style="padding-top:4px">
        <form class="form-horizontal">
          <div class="row form-group">
          <div class="col-12">

            <input type="text" id="pendingmails_searchInput" class="form-control text-center"
            style="::placeholder { color: red; }" placeholder="Search by Queue">
          </div>
          </div>
        </form>

        <table id="pendingmailstable" class="table table-responsive table-bordered table-striped table-hover"
          style="margin:4px">
          <thead class="bg-secondary text-white">
          <tr>
            <th scope="col">#</th>
            <th scope="col">Queue</th>
            <th scope="col">Attempts</th>
            <th scope="col">Date</th>
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
        function fetchpendingmailsData() {
          $.ajax({
          url: "{{ route('api.mailing.getalljobs') }}",
          type: 'GET',
          dataType: 'json',
          success: function (response) {
            populateallljobsTable(response);
          },
          error: function (xhr, status, error) {
            console.error('Error fetching data:', error);
          }
          });
        }

        // Function to search data using AJAX
        function searchpendingmailsData(searchTerm) {
          $.ajax({
          url: "{{ route('api.users.fetchsearchusers') }}",
          type: 'GET',
          dataType: 'json',
          data: {
            search: searchTerm
          },
          success: function (response) {
            populateallljobsTable(response);
          },
          error: function (xhr, status, error) {
            console.error('Error searching data:', error);
          }
          });
        }
        var viewjoburl = "{{ route('pages.mailing.viewjobpage', ['id' => '__ID__']) }}";

        // Function to populate table with data
        function populateallljobsTable(data) {
          var tbody = $('#pendingmailstable tbody');
          tbody.empty(); // Clear existing table rows
          if (data.length > 0) {
          $.each(data, function (index, data) {
            var viewjob_url = viewjoburl.replace('__ID__', data.id);
            var row = '<tr>' +
            '<td><a class="nav-link pt-0 pb-0" href="' + viewjob_url + '">' + data.id + '</a></td>' +
            '<td>' + data.queue + '</td>' +
            '<td>' + data.attempts + '</td>' +
            '<td>' + new Date(data.created_at).toISOString() + '</td>' +
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
        fetchpendingmailsData();

        // Search input keyup event
        $('#pendingmails_searchInput').on('keyup', function () {
          var searchTerm = $(this).val().toLowerCase();
          if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
          searchpendingmailsData(searchTerm);
          } else if (searchTerm.length === 0) {
          fetchpendingmailsData(); // Fetch all data when search input is empty
          }
        });
        });

      </script>
      </div>

      <div role="tabpanel" class="tab-pane" id="panel-failedmails">
      <div class="row">
        <div class="row form-group" style="padding-top:4px">
        <form class="form-horizontal">
          <div class="row form-group">
          <div class="col-12">

            <input type="text" id="failedjobs_searchInput" class="form-control text-center"
            style="::placeholder { color: red; }" placeholder="Search by Queue">
          </div>
          </div>
        </form>

        <table id="failedjobstable" class="table table-responsive table-bordered table-striped table-hover"
          style="margin:4px">
          <thead class="bg-secondary text-white">
          <tr>
            <th scope="col">#</th>
            <th scope="col">Queue</th>
            <th scope="col">Uuid</th>
            <th scope="col">Date</th>
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
        function fetchfailedjobsData() {
          $.ajax({
          url: "{{ route('api.mailing.getallfailedjobs') }}",
          type: 'GET',
          dataType: 'json',
          success: function (response) {
            populatefailedjobsTable(response);
          },
          error: function (xhr, status, error) {
            console.error('Error fetching data:', error);
          }
          });
        }

        // Function to search data using AJAX
        function searchfailedjobsData(searchTerm) {
          $.ajax({
          url: "{{ route('api.users.fetchsearchusers') }}",
          type: 'GET',
          dataType: 'json',
          data: {
            search: searchTerm
          },
          success: function (response) {
            populatefailedjobsTable(response);
          },
          error: function (xhr, status, error) {
            console.error('Error searching data:', error);
          }
          });
        }
        var viewfailedjoburl = "{{ route('pages.mailing.viewfailedjobdetails', ['id' => '__ID__']) }}";

        // Function to populate table with data
        function populatefailedjobsTable(data) {
          var tbody = $('#failedjobstable tbody');
          tbody.empty(); // Clear existing table rows
          if (data.length > 0) {
          $.each(data, function (index, data) {
            var viewfailedjob_url = viewfailedjoburl.replace('__ID__', data.id);
            var row = '<tr>' +
            '<td><a class="nav-link pt-0 pb-0" href="' + viewfailedjob_url + '">' + data.id + '</a></td>' +
            '<td>' + data.queue + '</td>' +
            '<td>' + data.uuid + '</td>' +
            '<td>' + new Date(data.failed_at).toISOString() + '</td>' +
            '</tr>';
            tbody.append(row);
          });
          }
          else {
          var row = '<tr><td colspan="5">No Users found</td></tr>';
          tbody.append(row);
          }
        }
        fetchfailedjobsData();

        // Search input keyup event
        $('#failedjobs_searchInput').on('keyup', function () {
          var searchTerm = $(this).val().toLowerCase();
          if (searchTerm.length >= 3) { // Optional: adjust the minimum search term length
            searchfailedjobsData(searchTerm);
          } else if (searchTerm.length === 0) {
            fetchfailedjobsData(); // Fetch all data when search input is empty
          }
        });
        });

      </script>
      </div>

    </div>

    </div>
  </div>


  </div>
@endauth
@endsection