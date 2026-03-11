<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Welcome, {{ Auth::user()->fullname }}!</h5>
                    <p class="card-text text-muted">You are logged in as an administrator.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
