@extends('layouts.app')
@section('title','Reports')
@section('page-title','Reports')
@section('sidebar-nav')@include('commander._nav')@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="page-header">
    <div><h1>Reports</h1><div class="breadcrumb">Commander / Reports</div></div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card"><div class="card-header"><h2>Incidents by Type</h2></div><div class="card-body"><canvas id="c1" height="220"></canvas></div></div>
    <div class="card"><div class="card-header"><h2>Tasks by Status</h2></div><div class="card-body"><canvas id="c2" height="220"></canvas></div></div>
</div>
<div class="grid-2">
    <div class="card"><div class="card-header"><h2>Incidents by Severity</h2></div><div class="card-body"><canvas id="c3" height="220"></canvas></div></div>
    <div class="card"><div class="card-header"><h2>Patients by Triage</h2></div><div class="card-body"><canvas id="c4" height="220"></canvas></div></div>
</div>

@push('scripts')
<script>
Chart.defaults.color='#7d8590'; Chart.defaults.font.family='Inter';
const opts = { responsive:true, plugins:{ legend:{ labels:{ color:'#7d8590', font:{family:'Inter',size:11} } } } };
new Chart(document.getElementById('c1'),{type:'doughnut',data:{labels:{!!json_encode($incidentsByType->pluck('type')->map(fn($t)=>ucfirst($t)))!!},datasets:[{data:{!!json_encode($incidentsByType->pluck('total'))!!},backgroundColor:['#f85149','#388bfd','#d29922','#3fb950','#bc8cff','#e85d04'],borderWidth:0}]},options:{...opts,cutout:'65%'}});
new Chart(document.getElementById('c2'),{type:'doughnut',data:{labels:{!!json_encode($tasksByStatus->pluck('status')->map(fn($s)=>ucfirst(str_replace('_',' ',$s))))!!},datasets:[{data:{!!json_encode($tasksByStatus->pluck('total'))!!},backgroundColor:['#d29922','#388bfd','#3fb950','#7d8590'],borderWidth:0}]},options:{...opts,cutout:'65%'}});
new Chart(document.getElementById('c3'),{type:'bar',data:{labels:{!!json_encode($incidentsBySeverity->pluck('severity')->map(fn($s)=>ucfirst($s)))!!},datasets:[{label:'Incidents',data:{!!json_encode($incidentsBySeverity->pluck('total'))!!},backgroundColor:['#3fb950','#d29922','#f85149','#ff6b6b'],borderRadius:6}]},options:{...opts,plugins:{...opts.plugins,legend:{display:false}},scales:{x:{grid:{color:'#30363d'}},y:{grid:{color:'#30363d'},ticks:{stepSize:1}}}}});
new Chart(document.getElementById('c4'),{type:'bar',data:{labels:{!!json_encode($patientsByTriage->pluck('triage_level')->map(fn($t)=>ucfirst($t)))!!},datasets:[{label:'Patients',data:{!!json_encode($patientsByTriage->pluck('total'))!!},backgroundColor:['#f85149','#d29922','#3fb950','#7d8590','#30363d'],borderRadius:6}]},options:{...opts,plugins:{...opts.plugins,legend:{display:false}},scales:{x:{grid:{color:'#30363d'}},y:{grid:{color:'#30363d'},ticks:{stepSize:1}}}}});
</script>
@endpush
@endsection
