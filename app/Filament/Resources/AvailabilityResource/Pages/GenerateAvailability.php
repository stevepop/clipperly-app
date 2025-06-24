<?php

namespace App\Filament\Resources\AvailabilityResource\Pages;

use App\Filament\Resources\AvailabilityResource;
use App\Models\Availability;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class GenerateAvailability extends Page
{
    protected static string $resource = AvailabilityResource::class;

    protected static string $view = 'filament.resources.availability-resource.pages.generate-availability';

    // Define all form fields as public properties
    public $start_date;
    public $end_date;
    public $days = [];
    public $start_time;
    public $end_time;
    public $max_appointments = 1;
    public $is_available = true;
    public $skip_existing = true;

    public function mount(): void
    {
        // Set default values
        $this->start_date = Carbon::today()->format('Y-m-d');
        $this->end_date = Carbon::today()->addMonths(3)->format('Y-m-d');
        $this->days = [1, 2, 3, 4, 5, 6]; // Monday to Saturday
        $this->start_time = '09:00';
        $this->end_time = '17:00';
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('start_date')
                ->required()
                ->label('Start Date'),
            DatePicker::make('end_date')
                ->required()
                ->label('End Date')
                ->after('start_date'),
            ToggleButtons::make('days')
                ->required()
                ->label('Days to Generate')
                ->options([
                    0 => 'Sun',
                    1 => 'Mon',
                    2 => 'Tue',
                    3 => 'Wed',
                    4 => 'Thu',
                    5 => 'Fri',
                    6 => 'Sat',
                ])
                ->multiple(),
            TimePicker::make('start_time')
                ->required()
                ->seconds(false),
            TimePicker::make('end_time')
                ->required()
                ->seconds(false)
                ->after('start_time'),
            Select::make('max_appointments')
                ->required()
                ->options([
                    1 => '1 appointment at a time',
                    2 => '2 appointments at a time',
                    3 => '3 appointments at a time',
                    4 => '4 appointments at a time',
                    5 => '5 appointments at a time',
                ]),
            Toggle::make('is_available')
                ->required()
                ->label('Available'),
            Toggle::make('skip_existing')
                ->required()
                ->label('Skip dates that already have availability'),
        ];
    }

    public function generate()
    {
        // Validate all form fields
        $this->validate();

        // Create a period between start and end date
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);

        $count = 0;

        // Loop through each day in the period
        foreach ($period as $date) {
            // Skip days not in the selected days array
            if (!in_array($date->dayOfWeek, $this->days)) {
                continue;
            }

            // Skip dates that already have availability if specified
            if ($this->skip_existing && Availability::where('date', $date->toDateString())->exists()) {
                continue;
            }

            // Create availability for the current date
            Availability::create([
                'date' => $date->toDateString(),
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'max_appointments' => $this->max_appointments,
                'is_available' => $this->is_available,
            ]);

            $count++;
        }

        // Show notification with the number of created availability slots
        Notification::make()
            ->title('Availability Generated')
            ->body("Successfully created {$count} availability slots.")
            ->success()
            ->send();

        // Redirect to the index page
        return redirect()->to(AvailabilityResource::getUrl());
    }

    protected function getActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate Availability')
                ->action('generate'),
        ];
    }
}
