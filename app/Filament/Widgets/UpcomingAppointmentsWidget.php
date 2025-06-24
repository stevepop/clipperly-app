<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingAppointmentsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Upcoming Appointments')
            ->query(
                Appointment::query()
                    ->whereIn('status', ['pending_payment', 'confirmed'])
                    ->where('appointment_time', '>', Carbon::now())
                    ->whereDate('appointment_time', '>', Carbon::today())
                    ->orderBy('appointment_time')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('appointment_time')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.duration')
                    ->label('Duration')
                    ->suffix(' min'),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'warning' => 'pending_payment',
                        'primary' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'gray' => 'no_show',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Appointment $record): bool => $record->status === 'pending_payment')
                    ->action(fn (Appointment $record) => $record->update(['status' => 'confirmed'])),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (Appointment $record): bool => in_array($record->status, ['pending_payment', 'confirmed']))
                    ->action(fn (Appointment $record) => $record->update(['status' => 'cancelled'])),
            ])
            ->emptyStateHeading('No upcoming appointments')
            ->emptyStateDescription('Future appointments will appear here.')
            ->poll('60s');
    }
}
