@extends('layouts.unauthorizedtemplate')

@section('content')
    <div class="row text-center">
        <h5>Select Admin User</h5>

        <form action="{{ route('api.makeinitialadmin') }}" method="POST">
            @csrf
            <select name="user_id" class="form-control" required>
                <option value="">-- Select User --</option>
                @foreach ($allusers as $user)
                    <option value="{{ $user->userid }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary mt-2">Make Admin</button>
        </form>

        @if (session('success'))
            <div class="alert alert-success mt-2">
                {{ session('success') }}
            </div>
        @endif
    </div>
@endsection
