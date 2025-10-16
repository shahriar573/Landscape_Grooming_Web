<?php

namespace App\Livewire\Staff;

use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VendorList extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all'; // all, active_sessions, my_owned, collaborating

    protected $queryString = ['search', 'filter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        $vendors = Vendor::query()
            ->with(['owner', 'collaborators.user', 'sessions' => function ($query) {
                $query->whereNull('ended_at')->latest('started_at');
            }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter === 'active_sessions', function ($query) {
                $query->whereHas('sessions', function ($q) {
                    $q->whereNull('ended_at');
                });
            })
            ->when($this->filter === 'my_owned', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->when($this->filter === 'collaborating', function ($query) use ($user) {
                $query->whereHas('collaborators', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.staff.vendor-list', [
            'vendors' => $vendors,
        ]);
    }
}
