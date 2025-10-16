<div>
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3">🤝 My Vendor Collaborations</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('staff.vendors.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Create New Vendor
            </a>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="🔍 Search vendors by name or email..."
                        wire:model.live.debounce.300ms="search"
                    >
                </div>
                <div class="col-md-6">
                    <select class="form-select" wire:model.live="filter">
                        <option value="all">All Vendors</option>
                        <option value="active_sessions">Active Sessions Only</option>
                        <option value="my_owned">My Owned Vendors</option>
                        <option value="collaborating">I'm Collaborating On</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors List -->
    @if($vendors->count() === 0)
        <div class="alert alert-info">
            <strong>No vendors found.</strong> 
            @if($search)
                Try adjusting your search criteria.
            @else
                Get started by creating your first vendor or waiting for an invitation.
            @endif
        </div>
    @else
        @foreach($vendors as $vendor)
            @php
                $userRole = $vendor->collaborators
                    ->where('user_id', auth()->id())
                    ->first()?->role ?? null;
                $isOwner = $vendor->owner_id === auth()->id();
                $activeSession = $vendor->sessions->first();
                $hasActiveSession = $activeSession !== null;
            @endphp

            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Vendor Info Column -->
                        <div class="col-md-7">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="fs-1">📦</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">
                                        {{ $vendor->name }}
                                        @if($hasActiveSession)
                                            <span class="badge bg-danger ms-2">🔴 Live</span>
                                        @elseif($vendor->is_active)
                                            <span class="badge bg-success ms-2">🟢 Active</span>
                                        @else
                                            <span class="badge bg-secondary ms-2">⚪ Idle</span>
                                        @endif
                                    </h5>
                                    <div class="text-muted small">
                                        <div>
                                            <strong>Owner:</strong> 
                                            @if($isOwner)
                                                You
                                            @else
                                                {{ $vendor->owner?->name ?? 'Unknown' }} ({{ $vendor->owner?->email }})
                                            @endif
                                        </div>
                                        <div>
                                            <strong>Your Role:</strong> 
                                            @if($isOwner)
                                                <span class="badge bg-warning text-dark">🔑 Owner</span>
                                            @elseif($userRole === 'manager')
                                                <span class="badge bg-info">⚙️ Manager</span>
                                            @elseif($userRole === 'participant')
                                                <span class="badge bg-primary">👥 Participant</span>
                                            @else
                                                <span class="badge bg-secondary">Viewer</span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>Collaborators:</strong> {{ $vendor->collaborators->count() }} member(s)
                                        </div>
                                        @if($hasActiveSession)
                                            <div class="text-danger">
                                                <strong>Active Session:</strong> 
                                                Started {{ $activeSession->started_at->diffForHumans() }} 
                                                by {{ $activeSession->startedBy?->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Column -->
                        <div class="col-md-5 text-end">
                            <div class="btn-group" role="group">
                                @if($hasActiveSession)
                                    <a href="{{ route('staff.vendors.session', $vendor) }}" 
                                       class="btn btn-danger btn-sm">
                                        🔴 Join Session
                                    </a>
                                @else
                                    <a href="{{ route('staff.vendors.show', $vendor) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        View Details
                                    </a>
                                @endif

                                @if($isOwner || $userRole === 'manager')
                                    <a href="{{ route('staff.vendors.manage', $vendor) }}" 
                                       class="btn btn-outline-secondary btn-sm">
                                        ⚙️ Manage
                                    </a>
                                @endif

                                @if(!$hasActiveSession && ($isOwner || $userRole === 'manager'))
                                    <button 
                                        wire:click="startSession({{ $vendor->id }})" 
                                        class="btn btn-success btn-sm">
                                        Start Session
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="mt-4">
            {{ $vendors->links() }}
        </div>
    @endif
</div>
