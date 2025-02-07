<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->relationship('patient', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => Patient::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('first_name')
                            ->required(),
                        Forms\Components\TextInput::make('last_name')
                            ->required(),
                        Forms\Components\TextInput::make('phone'),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                    ]),
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->required()
                    ->reactive(),
                Forms\Components\DatePicker::make('date')
                    ->seconds(false)
                    ->native(false)
                    ->minutesStep(30)
                    ->maxDate(now()->addMonth(2))
                    ->hidden(fn($get) => $get('doctor_id') === null)
                    ->disabledDates(dates: function ($get) {
                        return $get('doctor_id') === null ? [] : Doctor::find($get('doctor_id'))->getExcludedDates();
                    })
                    ->reactive()
                    ->required(),
                Select::make('start_time')
                    ->hidden(fn($get) => $get('doctor_id') === null)
                    ->options(function ($get) {
                        $doctorId = $get('doctor_id');
                        $date = $get('date');

                        if (!$doctorId || !$date) {
                            return [];
                        }

                        $doctor = Doctor::find($doctorId);

                        if ($doctor) {
                            $appointment = Appointment::find($get('id'));

                            return $doctor->formatTimeArrayForSelect($doctor->getAvailableTimes($date, $appointment));
                        }

                        return [];
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Time')
                    ->getStateUsing(function (Appointment $appointment) {
                        return Carbon::parse($appointment->start_time)->format('H:i') . ' - ' . Carbon::parse($appointment->end_time)->format('H:i');
                    }),
                Tables\Columns\TextColumn::make('patient.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
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
