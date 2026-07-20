<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Menejemen User';
    protected static ?string $slug = 'MasterUser';
    protected static ?string $navigationLabel = 'Master User';

    public static function getModelLabel(): string
    {
        return 'Master User';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->revealable()
                    ->maxLength(255),

                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->default(null)
                    ->required(),

                Forms\Components\TextInput::make('no_tlp')
                    ->maxLength(255)
                    ->default(null)
                    ->required(),

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('id')
                ->searchable()
                ->label('No ID'),

            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->label('Nama'),

            Tables\Columns\TextColumn::make('email')
                ->label('Email'),

            Tables\Columns\TextColumn::make('no_tlp')
                ->label('Telepon'),

            Tables\Columns\TextColumn::make('address')
                ->limit(30)
                ->label('Alamat'),

            Tables\Columns\TextColumn::make('roles.name')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'super_admin' => 'danger',
                    'Customer' => 'info',
                    'Kurir' => 'violet',
                    'Manager' => 'success',
                    'Owner' => 'cyan',
                    default => 'primary',
                })
                ->label('Role Akses'),
                ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari_tanggal'], fn($q) => $q->whereDate('created_at', '>=', $data['dari_tanggal']))
                            ->when($data['sampai_tanggal'], fn($q) => $q->whereDate('created_at', '<=', $data['sampai_tanggal']));
                    }),
            Tables\Filters\SelectFilter::make('roles')
                ->label('Pilih Role')
                ->relationship('roles', 'name')
                ->placeholder('Semua Role'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()
                ->successNotificationTitle('Produk Berhasil Dihapus!')
                ->modalCancelAction(function (\Filament\Actions\StaticAction $action) {
                    return $action
                        ->action(function () {
                            \Filament\Notifications\Notification::make()
                                ->title('Dibatalkan')
                                ->body('Produk tidak jadi dihapus.')
                                ->icon('heroicon-o-x-circle')
                                ->color('gray')
                                ->duration(3000)
                                ->send();
                        });
                }),
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('user Berhasil Dihapus!'),
                Tables\Actions\BulkAction::make('printRekapUser')
                    ->label('Rekap Akun Terpilih (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report-users', [
                            'items' => $records,
                            'title' => 'REKAPITULASI DATA PENGGUNA'
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "Rekap_User_" . now()->format('Y-m-d') . ".pdf");
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
