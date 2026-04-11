<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Commander\CommanderController;
use App\Http\Controllers\Responder\ResponderController;
use App\Http\Controllers\Citizen\CitizenController;

// ── Auth ─────────────────────────────────────────────────────────────────
Route::get('/',  [LoginController::class, 'showLoginForm'])->name('home');
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/register', [LoginController::class, 'register'])->name('register.post');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// ── Admin ─────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users
    Route::get('/users',               [AdminController::class, 'users'])->name('users');
    Route::get('/users/create',        [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users',              [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit',   [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}',        [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}',     [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Incidents
    Route::get('/incidents',                [AdminController::class, 'incidents'])->name('incidents');
    Route::get('/incidents/create',         [AdminController::class, 'createIncident'])->name('incidents.create');
    Route::post('/incidents',               [AdminController::class, 'storeIncident'])->name('incidents.store');
    Route::get('/incidents/{incident}/edit',[AdminController::class, 'editIncident'])->name('incidents.edit');
    Route::put('/incidents/{incident}',     [AdminController::class, 'updateIncident'])->name('incidents.update');
    Route::delete('/incidents/{incident}',  [AdminController::class, 'destroyIncident'])->name('incidents.destroy');

    // Resources
    Route::get('/resources',              [AdminController::class, 'resources'])->name('resources');
    Route::post('/resources',             [AdminController::class, 'storeResource'])->name('resources.store');
    Route::put('/resources/{resource}',   [AdminController::class, 'updateResource'])->name('resources.update');
    Route::delete('/resources/{resource}',[AdminController::class, 'destroyResource'])->name('resources.destroy');

    // Alerts
    Route::get('/alerts',           [AdminController::class, 'alerts'])->name('alerts');
    Route::post('/alerts',          [AdminController::class, 'storeAlert'])->name('alerts.store');
    Route::delete('/alerts/{alert}',[AdminController::class, 'destroyAlert'])->name('alerts.destroy');

    // Medical / Patients
    Route::get('/patients',               [AdminController::class, 'patients'])->name('patients');
    Route::post('/patients',              [AdminController::class, 'storePatient'])->name('patients.store');
    Route::put('/patients/{patient}',     [AdminController::class, 'updatePatient'])->name('patients.update');

    // Training
    Route::get('/training',                       [AdminController::class, 'training'])->name('training');
    Route::post('/training',                      [AdminController::class, 'storeTraining'])->name('training.store');
    Route::put('/training/{program}',             [AdminController::class, 'updateTraining'])->name('training.update');

    // Shelters
    Route::get('/shelters',              [AdminController::class, 'shelters'])->name('shelters');
    Route::post('/shelters',             [AdminController::class, 'storeShelter'])->name('shelters.store');
    Route::put('/shelters/{shelter}',    [AdminController::class, 'updateShelter'])->name('shelters.update');

    // Reports & Audit
    Route::get('/reports',   [AdminController::class, 'reports'])->name('reports');
    Route::get('/audit-logs',[AdminController::class, 'auditLogs'])->name('audit_logs');
    Route::get('/settings',  [AdminController::class, 'settings'])->name('settings');
});

// ── Commander ─────────────────────────────────────────────────────────────
Route::prefix('commander')->name('commander.')->middleware(['auth','role:commander'])->group(function () {
    Route::get('/dashboard', [CommanderController::class, 'dashboard'])->name('dashboard');

    Route::get('/incidents',                      [CommanderController::class, 'incidents'])->name('incidents');
    Route::get('/incidents/create',               [CommanderController::class, 'createIncident'])->name('incidents.create');
    Route::post('/incidents',                     [CommanderController::class, 'storeIncident'])->name('incidents.store');
    Route::get('/incidents/{incident}',           [CommanderController::class, 'showIncident'])->name('incidents.show');
    Route::put('/incidents/{incident}',           [CommanderController::class, 'updateIncident'])->name('incidents.update');

    Route::get('/tasks',              [CommanderController::class, 'tasks'])->name('tasks');
    Route::post('/tasks',             [CommanderController::class, 'storeTask'])->name('tasks.store');
    Route::put('/tasks/{task}',       [CommanderController::class, 'updateTask'])->name('tasks.update');
    Route::delete('/tasks/{task}',    [CommanderController::class, 'destroyTask'])->name('tasks.destroy');

    Route::get('/resources',                  [CommanderController::class, 'resources'])->name('resources');
    Route::put('/resources/{resource}',       [CommanderController::class, 'updateResource'])->name('resources.update');

    Route::get('/alerts',     [CommanderController::class, 'alerts'])->name('alerts');
    Route::post('/alerts',    [CommanderController::class, 'storeAlert'])->name('alerts.store');

    Route::get('/comms',       [CommanderController::class, 'comms'])->name('comms');
    Route::post('/comms',      [CommanderController::class, 'sendMessage'])->name('comms.send');

    Route::get('/patients',           [CommanderController::class, 'patients'])->name('patients');
    Route::post('/patients',          [CommanderController::class, 'storePatient'])->name('patients.store');
    Route::put('/patients/{patient}', [CommanderController::class, 'updatePatient'])->name('patients.update');

    Route::get('/mapping', [CommanderController::class, 'mapping'])->name('mapping');
    Route::get('/reports', [CommanderController::class, 'reports'])->name('reports');
});

// ── Responder ─────────────────────────────────────────────────────────────
Route::prefix('responder')->name('responder.')->middleware(['auth','role:responder'])->group(function () {
    Route::get('/dashboard', [ResponderController::class, 'dashboard'])->name('dashboard');

    Route::get('/incidents',          [ResponderController::class, 'incidents'])->name('incidents');
    Route::get('/incidents/{incident}',[ResponderController::class, 'showIncident'])->name('incidents.show');

    Route::get('/tasks',                [ResponderController::class, 'tasks'])->name('tasks');
    Route::put('/tasks/{task}/status',  [ResponderController::class, 'updateTaskStatus'])->name('tasks.update_status');

    Route::get('/resources', [ResponderController::class, 'resources'])->name('resources');
    Route::get('/alerts',    [ResponderController::class, 'alerts'])->name('alerts');

    Route::get('/comms',  [ResponderController::class, 'comms'])->name('comms');
    Route::post('/comms', [ResponderController::class, 'sendMessage'])->name('comms.send');

    Route::get('/mapping', [ResponderController::class, 'mapping'])->name('mapping');

    Route::get('/training',  [ResponderController::class, 'training'])->name('training');
    Route::post('/training/enroll', [ResponderController::class, 'enrollTraining'])->name('training.enroll');
});

// ── Citizen ───────────────────────────────────────────────────────────────
Route::prefix('citizen')->name('citizen.')->middleware(['auth','role:citizen'])->group(function () {
    Route::get('/dashboard', [CitizenController::class, 'dashboard'])->name('dashboard');
    Route::get('/alerts',    [CitizenController::class, 'alerts'])->name('alerts');
    Route::get('/mapping',   [CitizenController::class, 'mapping'])->name('mapping');
    Route::get('/portal',    [CitizenController::class, 'portal'])->name('portal');
    Route::post('/portal',   [CitizenController::class, 'storeReport'])->name('portal.store');
    Route::get('/portal/{report}', [CitizenController::class, 'showReport'])->name('portal.show');
});
