{{-- Shared commander nav partial: resources/views/commander/_nav.blade.php --}}
<div class="sb-section">Operations</div>
<a href="{{ route('commander.dashboard') }}" class="{{ request()->routeIs('commander.dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard"></i> Dashboard
</a>
<a href="{{ route('commander.incidents') }}" class="{{ request()->routeIs('commander.incidents*') ? 'active' : '' }}">
    <i data-lucide="flame"></i> Incidents
</a>
<a href="{{ route('commander.tasks') }}" class="{{ request()->routeIs('commander.tasks*') ? 'active' : '' }}">
    <i data-lucide="check-square"></i> Tasks
</a>
<a href="{{ route('commander.resources') }}" class="{{ request()->routeIs('commander.resources*') ? 'active' : '' }}">
    <i data-lucide="package"></i> Resources
</a>
<a href="{{ route('commander.alerts') }}" class="{{ request()->routeIs('commander.alerts*') ? 'active' : '' }}">
    <i data-lucide="bell"></i> Alerts
</a>
<a href="{{ route('commander.comms') }}" class="{{ request()->routeIs('commander.comms*') ? 'active' : '' }}">
    <i data-lucide="message-circle"></i> Communications
</a>
<a href="{{ route('commander.patients') }}" class="{{ request()->routeIs('commander.patients*') ? 'active' : '' }}">
    <i data-lucide="heart-pulse"></i> Medical
</a>
<a href="{{ route('commander.mapping') }}" class="{{ request()->routeIs('commander.mapping') ? 'active' : '' }}">
    <i data-lucide="map"></i> Live Map
</a>
<a href="{{ route('commander.reports') }}" class="{{ request()->routeIs('commander.reports') ? 'active' : '' }}">
    <i data-lucide="bar-chart-2"></i> Reports
</a>
