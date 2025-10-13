<?php
namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('services.index', compact('services'));
    }
    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }
    // Admin: show form to create a service
    public function create()
    {
        $this->authorize('create', Service::class);
        return view('services.create');
    }
    // Admin: store a new service
    public function store(Request $request)
    {
        $this->authorize('create', Service::class);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'nullable|integer',
            'image_path' => 'nullable|string',
        ]);
        Service::create($data);
        return redirect()->route('services.index')->with('status', 'Service created.');
    }
    // Admin: edit form
    public function edit(Service $service)
    {
        $this->authorize('update', $service);
        return view('services.edit', compact('service'));
    }
    // Admin: update
    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'nullable|integer',
            'image_path' => 'nullable|string',
        ]);
        $service->update($data);
        return redirect()->route('services.index')->with('status', 'Service updated.');
    }
    // Admin: destroy
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return redirect()->route('services.index')->with('status', 'Service deleted.');
    }
}
