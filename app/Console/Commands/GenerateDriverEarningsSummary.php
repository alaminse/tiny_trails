<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\DriverCommission\app\Models\DriverEarningsSummary;

class GenerateDriverEarningsSummary extends Command
{
    protected $signature = 'driver:generate-earnings-summary 
                            {--date= : Date for summary generation (Y-m-d format)}
                            {--type=daily : Summary type (daily, weekly, monthly)}
                            {--driver= : Specific driver ID}';

    protected $description = 'Generate driver earnings summary for specified date and type';

    public function handle(): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();
        $summaryType = $this->option('type');
        $driverId = $this->option('driver');

        $this->info("Generating {$summaryType} earnings summary for " . $date->toDateString());

        $drivers = $driverId ? User::where('id', $driverId)->get() : User::where('role', 'driver')->get();
        
        $progressBar = $this->output->createProgressBar($drivers->count());
        $progressBar->start();

        foreach ($drivers as $driver) {
            $this->generateSummaryForDriver($driver, $date, $summaryType);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('Earnings summary generation completed!');

        return self::SUCCESS;
    }

    private function generateSummaryForDriver(User $driver, Carbon $date, string $summaryType): void
    {
        $dateRange = $this->getDateRange($date, $summaryType);
        
        $commissions = DriverCommission::byDriver($driver->id)
            ->dateRange($dateRange['start'], $dateRange['end'])
            ->get();

        $data = [
            'total_rides' => $commissions->where('commission_type', 'per_ride')->count(),
            'completed_rides' => $commissions->where('commission_type', 'per_ride')->count(), // Assuming all are completed
            'cancelled_rides' => 0, // You might need to get this from ride assignments
            'total_fare' => $commissions->sum('base_fare'),
            'total_commission' => $commissions->sum('commission_amount'),
            'total_bonus' => $commissions->sum('bonus_amount'),
            'total_penalty' => $commissions->sum('penalty_amount'),
            'net_earnings' => $commissions->sum('total_earning'),
            'completion_rate' => 100.0, // Calculate based on actual data
            'average_rating' => 4.5, // Get from actual ratings
            'total_distance_km' => 0, // Get from ride data
            'total_duration_minutes' => 0, // Get from ride data
        ];

        DriverEarningsSummary::createOrUpdateSummary(
            $driver->id,
            $date,
            $summaryType,
            $data
        );
    }

    private function getDateRange(Carbon $date, string $summaryType): array
    {
        return match ($summaryType) {
            'daily' => [
                'start' => $date->copy()->startOfDay(),
                'end' => $date->copy()->endOfDay(),
            ],
            'weekly' => [
                'start' => $date->copy()->startOfWeek(),
                'end' => $date->copy()->endOfWeek(),
            ],
            'monthly' => [
                'start' => $date->copy()->startOfMonth(),
                'end' => $date->copy()->endOfMonth(),
            ],
            default => throw new \InvalidArgumentException("Invalid summary type: {$summaryType}"),
        };
    }
}