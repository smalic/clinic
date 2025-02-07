<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('specialization_id')
                    ->relationship('specialization', 'name')
                    ->required(),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('Manage the Calendar')
                        ->form([
                            Repeater::make('calendars')
                                ->label('Select the time slots when the doctor will be visiting.')
                                ->addActionLabel('Add Slot')
                                ->relationship('calendars')
                                ->cloneable()
                                ->schema([
                                    Select::make('day_of_week')
                                        ->label('Day of the Week')
                                        ->options([
                                            'monday' => 'Monday',
                                            'tuesday' => 'Tuesday',
                                            'wednesday' => 'Wednesday',
                                            'thursday' => 'Thursday',
                                            'friday' => 'Friday',
                                        ])
                                        ->required(),
                                    TimePicker::make('start_time')
                                        ->seconds(false)
                                        ->datalist([
                                            '09:00',
                                            '09:30',
                                            '10:00',
                                            '10:30',
                                            '11:00',
                                            '11:30',
                                            '12:00',
                                            '12:30',
                                            '13:00',
                                            '13:30',
                                            '14:00',
                                            '14:30',
                                            '15:00',
                                            '15:30',
                                            '16:00',
                                            '16:30',
                                            '17:00',
                                            '17:30',
                                            '18:00',
                                            '18:30',
                                            '19:00',
                                            '19:30',
                                            '20:00',
                                            '20:30',
                                        ])
                                        ->required(),
                                    TimePicker::make('end_time')
                                        ->seconds(false)
                                        ->datalist([
                                            '09:00',
                                            '09:30',
                                            '10:00',
                                            '10:30',
                                            '11:00',
                                            '11:30',
                                            '12:00',
                                            '12:30',
                                            '13:00',
                                            '13:30',
                                            '14:00',
                                            '14:30',
                                            '15:00',
                                            '15:30',
                                            '16:00',
                                            '16:30',
                                            '17:00',
                                            '17:30',
                                            '18:00',
                                            '18:30',
                                            '19:00',
                                            '19:30',
                                            '20:00',
                                            '20:30',
                                        ])
                                        ->required(),
                                ])
                                ->columns(3)
                        ])
                        ->mountUsing(function (Form $form) {
                            $form->fill([]);
                        })
                        ->action(function (array $data, Doctor $doctor): void {
                            Notification::make()
                                ->title('Calendar updated successfully!')
                                ->success()
                                ->send();
                        }),
                    Forms\Components\Actions\Action::make('Manage the Calendar Exemptions')
                        ->form([
                            Repeater::make('calendar_exemptions')
                                ->label('If the doctor won\'t be visiting at a certain date, ensure it is logged here.')
                                ->addActionLabel('Add Slot')
                                ->relationship('calendar_exemptions')
                                ->cloneable()
                                ->schema([
                                    DatePicker::make('date')
                                        ->label('Date')
                                        ->required(),
                                    Checkbox::make('removed')
                                        ->reactive(),
                                    TimePicker::make('start_time')
                                        ->seconds(false)
                                        ->hidden(fn($get) => $get('removed') === true)
                                        ->datalist([
                                            '09:00',
                                            '09:30',
                                            '10:00',
                                            '10:30',
                                            '11:00',
                                            '11:30',
                                            '12:00',
                                            '12:30',
                                            '13:00',
                                            '13:30',
                                            '14:00',
                                            '14:30',
                                            '15:00',
                                            '15:30',
                                            '16:00',
                                            '16:30',
                                            '17:00',
                                            '17:30',
                                            '18:00',
                                            '18:30',
                                            '19:00',
                                            '19:30',
                                            '20:00',
                                            '20:30',
                                        ])
                                        ->required(),
                                    TimePicker::make('end_time')
                                        ->seconds(false)
                                        ->hidden(fn($get) => $get('removed') === true)
                                        ->datalist([
                                            '09:00',
                                            '09:30',
                                            '10:00',
                                            '10:30',
                                            '11:00',
                                            '11:30',
                                            '12:00',
                                            '12:30',
                                            '13:00',
                                            '13:30',
                                            '14:00',
                                            '14:30',
                                            '15:00',
                                            '15:30',
                                            '16:00',
                                            '16:30',
                                            '17:00',
                                            '17:30',
                                            '18:00',
                                            '18:30',
                                            '19:00',
                                            '19:30',
                                            '20:00',
                                            '20:30',
                                        ])
                                        ->required(),
                                ])
                                ->columns(4)
                        ])
                        ->mountUsing(function (Form $form) {
                            $form->fill([]);
                        })
                        ->action(function (array $data, Doctor $doctor): void {
                            Notification::make()
                                ->title('Calendar updated successfully!')
                                ->success()
                                ->send();
                        })
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('specialization.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
