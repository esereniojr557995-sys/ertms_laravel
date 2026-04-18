<?php
// app/Http/Controllers/Citizen/CitizenController.php
namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\{Alert, Shelter, CitizenReport, Incident};
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function dashboard()
    {
        $latestAlerts    = Alert::where('status', 'sent')->latest()->take(5)->get();
        $openShelters    = Shelter::where('status', 'open')->get();
        $myReports       = CitizenReport::where('user_id', auth()->id())->latest()->take(5)->get();
        $activeIncidents = Incident::whereIn('status', ['open', 'active'])->count();
        return view('citizen.dashboard', compact('latestAlerts', 'openShelters', 'myReports', 'activeIncidents'));
    }

    // ── Alerts (Read) ──────────────────────────────────────────────────────
    public function alerts()
    {
        $alerts = Alert::where('status', 'sent')->latest()->paginate(15);
        return view('citizen.alerts', compact('alerts'));
    }

    // ── Mapping (Read) ─────────────────────────────────────────────────────
    public function mapping()
    {
        $shelters  = Shelter::all();
        $incidents = Incident::whereIn('status', ['open', 'active'])->whereNotNull('latitude')->get();
        return view('citizen.mapping', compact('shelters', 'incidents'));
    }

    // ── Public Portal / Citizen Reports ───────────────────────────────────
    public function portal()
    {
        $myReports = CitizenReport::where('user_id', auth()->id())->latest()->paginate(10);
        return view('citizen.portal', compact('myReports'));
    }

    public function storeReport(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'required|string',
            'location'    => 'nullable|string|max:200',   // ← changed: optional
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'type'        => 'required|in:fire,flood,accident,medical,hazard,other',
            'photo'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('citizen_reports', 'public');
        }

        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';

        CitizenReport::create($data);

        return redirect()->route('citizen.portal')
            ->with('success', 'Report submitted. Thank you for helping keep our community safe!');
    }

    // ── Cancel a pending report ────────────────────────────────────────────
    public function cancelReport(CitizenReport $report)
    {
        // Only the owner can cancel, and only if still pending
        abort_unless($report->user_id === auth()->id(), 403);

        if ($report->status !== 'pending') {
            return redirect()->route('citizen.portal')
                ->with('error', 'Only pending reports can be cancelled.');
        }

        $report->update(['status' => 'dismissed']);

        return redirect()->route('citizen.portal')
            ->with('success', 'Report cancelled successfully.');
    }

    public function showReport(CitizenReport $report)
    {
        abort_unless($report->user_id === auth()->id(), 403);
        return view('citizen.report_show', compact('report'));
    }
}