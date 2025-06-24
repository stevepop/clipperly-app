<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AvailabilityResource\Pages;
use App\Models\Availability;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AvailabilityResource extends Resource
{
    protected static ?string $model = Availability::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Scheduling';

    protected static ?string $label = 'Slot';

    protected static ?string $pluralLabel = 'Slots';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->seconds(false),
                Forms\Components\TimePicker::make('end_time')
                    ->required()
                    ->seconds(false)
                    ->after('start_time'),
                Forms\Components\TextInput::make('max_appointments')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
                Forms\Components\Toggle::make('is_available')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_of_week')
                    ->getStateUsing(function (Availability $record) {
                        return $record->date->format('l');
                    })
                    ->label('Day'),
                Tables\Columns\TextColumn::make('start_time')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_appointments')
                    ->label('Capacity')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->label('Available'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_available')
                    ->toggle()
                    ->label('Available Only')
                    ->query(fn ($query) => $query->where('is_available', true)),
                Tables\Filters\Filter::make('upcoming')
                    ->toggle()
                    ->label('Upcoming Only')
                    ->query(fn ($query) => $query->where('date', '>=', Carbon::today())),
                Tables\Filters\Filter::make('past')
                    ->toggle()
                    ->label('Past Only')
                    ->query(fn ($query) => $query->where('date', '<', Carbon::today())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggleAvailability')
                        ->label('Toggle Availability')
                        ->icon('heroicon-o-calendar')
                        ->action(function (Collection $records): void {
                            $records->each(function (Availability $record): void {
                                $record->update(['is_available' => !$record->is_available]);
                            });
                        }),
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
            'index' => Pages\ListAvailabilities::route('/'),
            'create' => Pages\CreateAvailability::route('/create'),
            'edit' => Pages\EditAvailability::route('/{record}/edit'),
            'generate' => Pages\GenerateAvailability::route('/generate'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('date', '>=', Carbon::today())
            ->where('is_available', true)
            ->count();
    }
}
