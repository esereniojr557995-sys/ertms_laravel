@extends('layouts.app')
@section('title','Reports')
@section('page-title','Reports & Analytics')

@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
<a href="{{ route('admin.incidents') }}"><i data-lucide="flame"></i> Incidents</a>
<a href="{{ route('admin.citizen_reports') }}" class="{{ request()->routeIs('admin.citizen_reports*') ? 'active' : '' }}">
    <i data-lucide="file-text"></i> Citizen Reports
</a>
<a href="{{ route('admin.resources') }}"><i data-lucide="package"></i> Resources</a>
<a href="{{ route('admin.alerts') }}"><i data-lucide="bell"></i> Alerts</a>
<a href="{{ route('admin.comms') }}" class="{{ request()->routeIs('admin.comms*') ? 'active' : '' }}">
    <i data-lucide="message-square"></i> Communications
</a>
<a href="{{ route('admin.patients') }}"><i data-lucide="heart-pulse"></i> Medical</a>
<a href="{{ route('admin.training') }}"><i data-lucide="graduation-cap"></i> Training</a>
<a href="{{ route('admin.shelters') }}"><i data-lucide="map-pin"></i> Shelters</a>
<div class="sb-section">Management</div>
<a href="{{ route('admin.users') }}"><i data-lucide="users"></i> Users</a>
<a href="{{ route('admin.reports') }}" class="active"><i data-lucide="bar-chart-2"></i> Reports</a>
<a href="{{ route('admin.audit_logs') }}"><i data-lucide="scroll-text"></i> Audit Logs</a>
<a href="{{ route('admin.settings') }}"><i data-lucide="settings"></i> Settings</a>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="page-header">
    <div><h1>Analytics & Reports</h1><div class="bc">Admin / Reports</div></div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header"><h2><i data-lucide="pie-chart" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Incidents by Type</h2></div>
        <div class="card-body"><canvas id="chartByType" height="220"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><h2><i data-lucide="bar-chart-2" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Incidents by Severity</h2></div>
        <div class="card-body"><canvas id="chartBySeverity" height="220"></canvas></div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header"><h2><i data-lucide="activity" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Tasks by Status</h2></div>
        <div class="card-body"><canvas id="chartTasks" height="220"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><h2><i data-lucide="heart-pulse" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Patients by Triage</h2></div>
        <div class="card-body"><canvas id="chartTriage" height="220"></canvas></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2><i data-lucide="trending-up" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px"></i>Monthly Incidents — {{ date('Y') }}</h2></div>
    <div class="card-body"><canvas id="chartMonthly" height="100"></canvas></div>
</div>

@push('scripts')
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const chartDefaults = { responsive: true, plugins: { legend: { labels: { color: '#7d8590', font: { family:'Inter', size:11 } } } } };
Chart.defaults.color = '#7d8590';
Chart.defaults.font.family = 'Inter';

// Incidents by Type
new Chart(document.getElementById('chartByType'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($incidentsByType->pluck('type')->map(fn($t)=>ucfirst($t))) !!},
        datasets: [{
            data: {!! json_encode($incidentsByType->pluck('total')) !!},
            backgroundColor: ['#f85149','#388bfd','#d29922','#3fb950','#bc8cff','#e85d04','#58a6ff','#7d8590'],
            borderWidth: 0,
        }]
    },
    options: { ...chartDefaults, cutout: '65%' }
});

// Incidents by Severity
new Chart(document.getElementById('chartBySeverity'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($incidentsBySeverity->pluck('severity')->map(fn($s)=>ucfirst($s))) !!},
        datasets: [{
            label: 'Incidents',
            data: {!! json_encode($incidentsBySeverity->pluck('total')) !!},
            backgroundColor: ['#3fb950','#d29922','#f85149','#ff6b6b'],
            borderRadius: 6,
        }]
    },
    options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { display: false } }, scales: { x: { grid: { color: '#30363d' } }, y: { grid: { color: '#30363d' }, ticks: { stepSize: 1 } } } }
});

// Tasks by Status
new Chart(document.getElementById('chartTasks'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($tasksByStatus->pluck('status')->map(fn($s)=>ucfirst(str_replace('_',' ',$s)))) !!},
        datasets: [{
            data: {!! json_encode($tasksByStatus->pluck('total')) !!},
            backgroundColor: ['#d29922','#388bfd','#3fb950','#7d8590'],
            borderWidth: 0,
        }]
    },
    options: { ...chartDefaults, cutout: '65%' }
});

// Triage
new Chart(document.getElementById('chartTriage'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($patientsByTriage->pluck('triage_level')->map(fn($t)=>ucfirst($t))) !!},
        datasets: [{
            label: 'Patients',
            data: {!! json_encode($patientsByTriage->pluck('total')) !!},
            backgroundColor: ['#f85149','#d29922','#3fb950','#7d8590','#30363d'],
            borderRadius: 6,
        }]
    },
    options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { display: false } }, scales: { x: { grid: { color: '#30363d' } }, y: { grid: { color: '#30363d' }, ticks: { stepSize: 1 } } } }
});

// Monthly
const monthlyData = new Array(12).fill(0);
@foreach($monthly as $m)
monthlyData[{{ $m->month - 1 }}] = {{ $m->total }};
@endforeach
new Chart(document.getElementById('chartMonthly'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Incidents',
            data: monthlyData,
            borderColor: '#e85d04',
            backgroundColor: 'rgba(232,93,4,.1)',
            pointBackgroundColor: '#e85d04',
            tension: 0.4, fill: true,
        }]
    },
    options: { ...chartDefaults, scales: { x: { grid: { color: '#30363d' } }, y: { grid: { color: '#30363d' }, ticks: { stepSize: 1 }, beginAtZero: true } } }
});
</script>
@endpush
@endsection
