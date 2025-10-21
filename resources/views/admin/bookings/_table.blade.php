<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bookings</h5>
        @isset($bookings)
            <span class="badge bg-secondary">{{ method_exists($bookings, 'total') ? $bookings->total() : $bookings->count() }} total</span>
        @endisset
    </div>
    <div class="card-body p-0">
        @if(($bookings ?? collect())->count() === 0)
            <div class="p-4 text-muted text-center">
                No bookings found.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Scheduled</th>
                            <th>Service</th>
                            <th>Customer</th>
                            <th>Staff Assignment</th>
                            <th>Status</th>
                            <th class="text-end">Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <strong>#{{ $booking->id }}</strong>
                                    <br><small class="text-muted">{{ $booking->created_at->format('M j') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $booking->scheduled_at->format('M j, Y') }}</strong>
                                    <br><small class="text-muted">{{ $booking->scheduled_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $booking->service->name }}</strong>
                                    @if($booking->service->duration)
                                        <br><small class="text-muted">{{ $booking->service->duration }} min</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $booking->customer->name }}</strong>
                                    <br><small class="text-muted">{{ $booking->customer->email }}</small>
                                    @if($booking->customer->mobile)
                                        <br><small class="text-muted">📱 {{ $booking->customer->mobile }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->staff)
                                        <span class="badge bg-success">
                                            👤 {{ $booking->staff->name }}
                                        </span>
                                        <br><small class="text-muted">{{ $booking->staff->email }}</small>
                                    @else
                                        <span class="badge bg-warning text-dark">⚠️ Unassigned</span>
                                        <br>
                                        <form method="POST" action="{{ route('bookings.assign-staff', $booking) }}" class="mt-1">
                                            @csrf
                                            <div class="input-group input-group-sm">
                                                <select name="staff_id" class="form-select form-select-sm" required aria-label="Assign staff member">
                                                    <option value="">Select Staff...</option>
                                                    @php
                                                        $availableStaff = \App\Models\User::where('role', 'staff')->get();
                                                    @endphp
                                                    @foreach($availableStaff as $staff)
                                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Assign</button>
                                            </div>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'secondary',
                                            'confirmed' => 'primary',
                                            'in_progress' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                        ];
                                        $statusColor = $statusColors[$booking->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong>${{ number_format($booking->price, 2) }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#details-{{ $booking->id }}">
                                            👁️
                                        </button>
                                        @if($booking->customer->mobile)
                                            <a href="tel:{{ $booking->customer->mobile }}" class="btn btn-sm btn-outline-success" title="Call Customer">
                                                📞
                                            </a>
                                        @endif
                                        <a href="mailto:{{ $booking->customer->email }}" class="btn btn-sm btn-outline-primary" title="Email Customer">
                                            ✉️
                                        </a>
                                    </div>
                                    <div id="details-{{ $booking->id }}" class="collapse mt-2">
                                        <div class="card card-body">
                                            <dl class="row mb-0 small">
                                                <dt class="col-4">Booking ID:</dt>
                                                <dd class="col-8">#{{ $booking->id }}</dd>
                                                <dt class="col-4">Created:</dt>
                                                <dd class="col-8">{{ $booking->created_at->format('M j, Y g:i A') }}</dd>
                                                @if($booking->notes)
                                                    <dt class="col-4">Notes:</dt>
                                                    <dd class="col-8">{{ $booking->notes }}</dd>
                                                @endif
                                                <dt class="col-4">Service Details:</dt>
                                                <dd class="col-8">{{ $booking->service->description ?? 'No description' }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if(method_exists($bookings ?? null, 'hasPages') && $bookings->hasPages())
        <div class="card-footer">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    @endif
</div>