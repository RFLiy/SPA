<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuperAdminResource\Pages;
use App\Filament\Resources\SuperAdminResource\RelationManagers;
use App\Models\SuperAdmin;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuperAdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Master Admin';
    protected static ?string $slug = 'SuperAdmin';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationBadgeTooltip = 'Total Super Admin';
    protected static ?string $navigationGroup = 'Menejemen User';

    public static function getModelLabel(): string
    {
        return 'Master Admin';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Kurir')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required(),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required(),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required(fn(string $context): bool => $context === 'create')
                        ->dehydrated(fn($state) => filled($state))
                        ->revealable()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('no_tlp')
                        ->tel()
                        ->label('No. WhatsApp')
                        ->required(),
                    Forms\Components\Textarea::make('address')
                        ->label('Alamat Lengkap')
                        ->required(),

                    Forms\Components\Hidden::make('role')
                        ->default('customer'),
                ])->columns(2),
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
                    ->label('Status')
                    ->color('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Produk Berhasil Dihapus!')
                    ->cancelAction(
                        fn($action) => $action
                            ->action(function () {
                                \Filament\Notifications\Notification::make()
                                    ->title('Dibatalkan')
                                    ->body('Produk tidak jadi dihapus.')
                                    ->icon('heroicon-o-x-circle')
                                    ->color('gray')
                                    ->duration(3000)
                                    ->send();
                            })
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuperAdmins::route('/'),
            'create' => Pages\CreateSuperAdmin::route('/create'),
            'edit' => Pages\EditSuperAdmin::route('/{record}/edit'),
        ];
    }
}
