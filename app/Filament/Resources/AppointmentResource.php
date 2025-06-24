<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use Illuminate\Support\Collection;
use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Scheduling';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('customer_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_phone')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\DateTimePicker::make('appointment_time')
                    ->required()
                    ->label('Appointment Time')
                    ->seconds(false),
                Forms\Components\TextInput::make('booking_code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending_payment' => 'Pending Payment',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('appointment_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending_payment',
                        'primary' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'gray' => 'no_show',
                    ]),
                Tables\Columns\TextColumn::make('booking_code')
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('customer_email')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_payment' => 'Pending Payment',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ]),
                Tables\Filters\Filter::make('upcoming')
                    ->toggle()
                    ->label('Upcoming Only')
                    ->query(fn ($query) => $query->where('appointment_time', '>=', Carbon::now())),
                Tables\Filters\Filter::make('past')
                    ->toggle()
                    ->label('Past Only')
                    ->query(fn ($query) => $query->where('appointment_time', '<', Carbon::now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Appointment $record): bool => $record->status === 'pending_payment')
                    ->action(fn (Appointment $record) => $record->update(['status' => 'confirmed'])),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Appointment $record): bool => in_array($record->status, ['pending_payment', 'confirmed']))
                    ->action(fn (Appointment $record) => $record->update(['status' => 'completed'])),
                Tables\Actions\Action::make('no_show')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->visible(fn (Appointment $record): bool => in_array($record->status, ['pending_payment', 'confirmed']))
                    ->action(fn (Appointment $record) => $record->update(['status' => 'no_show'])),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (Appointment $record): bool => in_array($record->status, ['pending_payment', 'confirmed']))
                    ->action(fn (Appointment $record) => $record->update(['status' => 'cancelled'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('confirmSelected')
                        ->label('Confirm Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'confirmed'])),
                    Tables\Actions\BulkAction::make('cancelSelected')
                        ->label('Cancel Selected')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'cancelled'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['pending_payment', 'confirmed'])
            ->where('appointment_time', '>=', Carbon::now())
            ->count();
    }
}
