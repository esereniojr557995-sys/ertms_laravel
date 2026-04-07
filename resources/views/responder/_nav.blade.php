{{-- resources/views/responder/_nav.blade.php --}}
@section('sidebar-nav')
<div class="sb-section">Operations</div>
<a href="{{ route('responder.dashboard') }}" class="{{ request()->routeIs('responder.dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard"></i> Dashboard
</a>
<a href="{{ route('responder.incidents') }}" class="{{ request()->routeIs('responder.incidents*') ? 'active' : '' }}">
    <i data-lucide="flame"></i> Incidents
</a>
<a href="{{ route('responder.tasks') }}" class="{{ request()->routeIs('responder.tasks*') ? 'active' : '' }}">
    <i data-lucide="check-square"></i> My Tasks
</a>
<a href="{{ route('responder.resources') }}" class="{{ request()->routeIs('responder.resources*') ? 'active' : '' }}">
    <i data-lucide="package"></i> Resources
</a>
<a href="{{ route('responder.alerts') }}" class="{{ request()->routeIs('responder.alerts*') ? 'active' : '' }}">
    <i data-lucide="bell"></i> Alerts
</a>
<a href="{{ route('responder.comms') }}" class="{{ request()->routeIs('responder.comms*') ? 'active' : '' }}">
    <i data-lucide="message-circle"></i> Communications
</a>
<a href="{{ route('responder.mapping') }}" class="{{ request()->routeIs('responder.mapping') ? 'active' : '' }}">
    <i data-lucide="map"></i> Live Map
</a>
<a href="{{ route('responder.training') }}" class="{{ request()->routeIs('responder.training*') ? 'active' : '' }}">
    <i data-lucide="graduation-cap"></i> Training
</a>
