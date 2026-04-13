@extends('layouts.app')
@section('title','New Incident')
@section('page-title','Create Incident')
@section('sidebar-nav')@include('commander._nav')@endsection
@section('content')
<div class="page-header">
    <div><h1>New Incident</h1><div class="bc">Commander / Incidents / Create</div></div>
    <a href="{{ route('commander.incidents') }}" class="btn btn-secondary"><i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back</a>
</div>
<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('commander.incidents.store') }}">
            @csrf
            <div class="form-group"><label>Incident Title *</label><input type="text" name="title" value="{{ old('title') }}" class="form-control" required></div>
            <div class="form-row">
                <div class="form-group">
                    <label>Type *</label>
                    <select name="type" class="form-control" required>
                        <option value="">— Select —</option>
                        @foreach(['fire','flood','earthquake','medical','rescue','hazmat','wind','other'] as $t)
                        <option value="{{ $t }}" {{ old('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Severity *</label>
                    <select name="severity" class="form-control" required>
                        @foreach(['low','moderate','high','critical'] as $s)
                        <option value="{{ $s }}" {{ old('severity')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Location *</label><input type="text" name="location" value="{{ old('location') }}" class="form-control" required></div>
            <div class="form-row">
                <div class="form-group"><label>Latitude</label><input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="form-control"></div>
                <div class="form-group"><label>Longitude</label><input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="form-control"></div>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" class="form-control" required>
                    @foreach(['open','active','contained','closed'] as $s)
                    <option value="{{ $s }}" {{ old('status','open')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea></div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:14px;height:14px"></i> Create Incident</button>
                <a href="{{ route('commander.incidents') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
