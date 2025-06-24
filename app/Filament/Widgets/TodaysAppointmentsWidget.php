<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodaysAppointmentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Today\'s Appointments')
            ->query(
                Appointment::query()
                    ->whereDate('appointment_time', Carbon::today())
                    ->orderBy('appointment_time')
            )
            ->columns([
                Tables\Columns\TextColumn::make('appointment_time')
                    ->label('Time')
                    ->dateTime('g:i A')
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
                Tables\Columns\TextColumn::make('booking_code')
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('status')
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
                    ->visible(fn(Appointment $record): bool => $record->status === 'pending_payment')
                    ->action(fn(Appointment $record) => $record->update(['status' => 'confirmed'])),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Appointment $record): bool => in_array($record->status, ['pending_payment', 'confirmed']))
                    ->action(fn(Appointment $record) => $record->update(['status' => 'completed'])),
                Tables\Actions\Action::make('no_show')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->visible(fn(Appointment $record): bool => in_array($record->status, ['pending_payment', 'confirmed']))
                    ->action(fn(Appointment $record) => $record->update(['status' => 'no_show'])),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn(Appointment $record): bool => in_array($record->status, ['pending_payment', 'confirmed']))
                    ->action(fn(Appointment $record) => $record->update(['status' => 'cancelled'])),
            ])
            ->emptyStateHeading('No appointments for today')
            ->emptyStateDescription('When customers book appointments for today, they will appear here.')
            ->poll('60s');
    }
}
