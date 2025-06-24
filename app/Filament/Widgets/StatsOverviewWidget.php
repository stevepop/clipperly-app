<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Count today's appointments
        $todayAppointments = Appointment::whereDate('appointment_time', Carbon::today())->count();

        // Count upcoming appointments
        $upcomingAppointments = Appointment::whereIn('status', ['pending_payment', 'confirmed'])
            ->where('appointment_time', '>', Carbon::now())
            ->count();

        // Count active services
        $activeServices = Service::where('is_active', true)->count();

        // Calculate completion rate (completed appointments / all past appointments)
        $pastAppointments = Appointment::where('appointment_time', '<', Carbon::now())->count();
        $completedAppointments = Appointment::where('status', 'completed')
            ->where('appointment_time', '<', Carbon::now())
            ->count();

        $completionRate = $pastAppointments > 0
            ? round(($completedAppointments / $pastAppointments) * 100)
            : 0;

        return [
            Stat::make('Today\'s Appointments', $todayAppointments)
                ->description('Scheduled for today')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Upcoming Appointments', $upcomingAppointments)
                ->description('Future bookings')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('Active Services', $activeServices)
                ->description('Available for booking')
                ->descriptionIcon('heroicon-m-scissors')
                ->color('warning'),

            Stat::make('Completion Rate', $completionRate . '%')
                ->description($completedAppointments . ' of ' . $pastAppointments . ' appointments completed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($completionRate >= 80 ? 'success' : ($completionRate >= 50 ? 'warning' : 'danger')),
        ];
    }
}
