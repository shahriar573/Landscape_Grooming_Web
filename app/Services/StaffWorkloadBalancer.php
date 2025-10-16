<?php
namespace App\Services;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
class StaffWorkloadBalancer
{
    public function __construct(private ExternalPollutionNotifier $notifier)
    {
    }

    public function rebalanceForService(Service $service): void
    {
        if (! Service::requiresBalancing($service->name)) {
            return;
        }

        DB::transaction(function () use ($service) {
            $bookings = Booking::where('service_id', $service->id)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->orderBy('scheduled_at')
                ->lockForUpdate()
                ->get();

            $staffMembers = User::where('role', 'staff')->orderBy('id')->get();

            if ($staffMembers->isEmpty()) {
                $this->notifier->notifyLoss($this->buildPayload($service->name, $bookings->count(), 0));
                return;
            }

            $loadMap = $this->initializeLoadMap($staffMembers);

            foreach ($bookings as $booking) {
                $targetStaffId = $this->pickStaffId($loadMap);

                if ($targetStaffId === null) {
                    continue;
                }

                $dirty = false;

                if ($booking->staff_id !== $targetStaffId) {
                    $booking->staff_id = $targetStaffId;
                    $dirty = true;
                }

                if ($booking->status === 'pending') {
                    $booking->status = 'confirmed';
                    $dirty = true;
                }

                if ($dirty) {
                    $booking->save();
                }

                $loadMap[$targetStaffId]++;
            }

            $unassigned = $bookings->whereNull('staff_id')->count();

            if ($unassigned > 0) {
                $this->notifier->notifyLoss($this->buildPayload($service->name, $unassigned, $staffMembers->count()));
            }
        });
    }

    protected function initializeLoadMap(Collection $staffMembers): array
    {
        $loadMap = [];

        foreach ($staffMembers as $staff) {
            $loadMap[$staff->id] = 0;
        }

        return $loadMap;
    }

    protected function pickStaffId(array $loadMap): ?int
    {
        if (empty($loadMap)) {
            return null;
        }

        $minLoad = min($loadMap);

        $candidates = array_keys(array_filter(
            $loadMap,
            fn ($load) => $load === $minLoad
        ));

        return $candidates[0] ?? null;
    }

    protected function buildPayload(string $serviceName, int $unassignedCount, int $availableStaff): array
    {
        return [
            'service' => $serviceName,
            'unassigned_bookings' => $unassignedCount,
            'available_staff' => $availableStaff,
            'discovered_at' => Carbon::now()->toIso8601String(),
            'message' => 'Landscape segment requires attention due to insufficient staffing.',
        ];
    }
}
