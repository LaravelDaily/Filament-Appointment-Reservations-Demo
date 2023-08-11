<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\Pages\CreateReservation;
use App\Filament\Resources\ReservationResource\Pages\EditReservation;
use App\Filament\Resources\ReservationResource\Pages\ListReservations;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use App\Models\Track;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $dateFormat = 'Y-m-d';

        return $form
            ->schema([
                DatePicker::make('date')
                    ->native(false)
                    ->minDate(now()->format($dateFormat))
                    ->maxDate(now()->addWeeks(2)->format($dateFormat))
                    ->format($dateFormat)
                    ->required()
                    ->live(),
                Radio::make('track')
                    ->options(fn (Get $get) => self::getReservations($get))
                    ->hidden(fn (Get $get) => ! $get('date'))
                    ->required()
                    ->columnSpan(2),
            ]);
    }

    public static function getReservations(Get $get)
    {
        $date                  = Carbon::parse($get('date'));
        $startPeriod           = $date->copy()->hour(14);
        $endPeriod             = $date->copy()->hour(16);
        $times                 = CarbonPeriod::create($startPeriod, '1 hour', $endPeriod);
        $availableReservations = [];

        $tracks = Track::with([
            'reservations' => function ($q) use ($startPeriod, $endPeriod) {
                $q->whereBetween('start_time', [$startPeriod, $endPeriod]);
            },
        ])
            ->get();

        foreach ($tracks as $track) {
            $reservations = $track->reservations->pluck('start_time')->toArray();

            $availableTimes = $times->copy()->filter(function ($time) use ($reservations) {
                return ! in_array($time, $reservations);
            })->toArray();

            foreach ($availableTimes as $time) {
                $key                         = $track->id . '-' . $time->format('H');
                $availableReservations[$key] = $track->title . ' ' . $time->format('H:i');
            }
        }

        return $availableReservations;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('track.title'),
                TextColumn::make('start_time')->dateTime('Y-m-d H:i'),
                TextColumn::make('end_time')->dateTime('Y-m-d H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('start_time', 'desc');
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
            'index'  => ListReservations::route('/'),
            'create' => CreateReservation::route('/create'),
            // 'edit'   => EditReservation::route('/{record}/edit'),
        ];
    }
}
