@extends('layouts.app')

@section('title', 'Services Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">🌿 Services Management</h1>
            <div class="btn-group">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Dashboard</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                    <i class="fas fa-plus"></i> Add Service
                </button>
            </div>
        </div>

        <!-- Service Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $totalServices }}</h4>
                                <p class="card-text">Total Services</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-concierge-bell fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $activeServices }}</h4>
                                <p class="card-text">Active Services</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $totalBookings }}</h4>
                                <p class="card-text">Total Bookings</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card text-white bg-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">${{ number_format($avgServicePrice, 2) }}</h4>
                                <p class="card-text">Avg. Service Price</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.services') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Services</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Service name or description...">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <option value="lawn_care" {{ request('category') === 'lawn_care' ? 'selected' : '' }}>Lawn Care</option>
                            <option value="landscaping" {{ request('category') === 'landscaping' ? 'selected' : '' }}>Landscaping</option>
                            <option value="tree_service" {{ request('category') === 'tree_service' ? 'selected' : '' }}>Tree Service</option>
                            <option value="maintenance" {{ request('category') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="seasonal" {{ request('category') === 'seasonal' ? 'selected' : '' }}>Seasonal</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sort By</label>
                        <div class="input-group">
                            <select class="form-select" id="sort" name="sort">
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                                <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>Price</option>
                                <option value="bookings_count" {{ request('sort') === 'bookings_count' ? 'selected' : '' }}>Popularity</option>
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Services Table -->
        <div class="card">
            <div class="card-header">
                <h5>Services List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Bookings</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $service->name }}</strong>
                                        @if($service->description)
                                            <br><small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $service->category ?? 'general')) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>${{ number_format($service->price, 2) }}</strong>
                                    @if($service->price_type === 'hourly')
                                        <small class="text-muted">/hour</small>
                                    @elseif($service->price_type === 'per_sqft')
                                        <small class="text-muted">/sq ft</small>
                                    @endif
                                </td>
                                <td>{{ $service->duration ?? 60 }} min</td>
                                <td>
                                    <span class="badge bg-info">{{ $service->bookings_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $service->is_active ? 'success' : 'danger' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" data-bs-toggle="modal" 
                                                data-bs-target="#viewServiceModal{{ $service->id }}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning" data-bs-toggle="modal" 
                                                data-bs-target="#editServiceModal{{ $service->id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.services.toggle', $service) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-{{ $service->is_active ? 'secondary' : 'success' }}" 
                                                    title="{{ $service->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $service->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this service?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No services found. <a href="#" data-bs-toggle="modal" data-bs-target="#createServiceModal">Add the first service</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($services->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $services->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Service Modal -->
<div class="modal fade" id="createServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.services.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Service Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="lawn_care">Lawn Care</option>
                                <option value="landscaping">Landscaping</option>
                                <option value="tree_service">Tree Service</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="seasonal">Seasonal</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="price" class="form-label">Price *</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label for="price_type" class="form-label">Price Type</label>
                            <select class="form-select" id="price_type" name="price_type">
                                <option value="fixed">Fixed Price</option>
                                <option value="hourly">Per Hour</option>
                                <option value="per_sqft">Per Square Foot</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="duration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" value="60">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection