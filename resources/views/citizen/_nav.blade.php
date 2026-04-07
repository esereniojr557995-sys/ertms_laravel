{{-- resources/views/citizen/_nav.blade.php --}}
<div class="sidebar-section">Community</div>
<a href="{{ route('citizen.dashboard') }}" class="{{ request()->routeIs('citizen.dashboard') ? 'active' : '' }}">
    <i data-lucide="home"></i> Home
</a>
<a href="{{ route('citizen.alerts') }}" class="{{ request()->routeIs('citizen.alerts') ? 'active' : '' }}">
    <i data-lucide="bell"></i> Alerts & Advisories
</a>
<a href="{{ route('citizen.mapping') }}" class="{{ request()->routeIs('citizen.mapping') ? 'active' : '' }}">
    <i data-lucide="map"></i> Evacuation Map
</a>
<a href="{{ route('citizen.portal') }}" class="{{ request()->routeIs('citizen.portal*') ? 'active' : '' }}">
    <i data-lucide="file-plus"></i> Report Incident
</a>
