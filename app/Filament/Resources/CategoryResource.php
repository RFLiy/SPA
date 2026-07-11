<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Master Kategori';
    protected static ?string $slug = 'MasterKategori';
    protected static ?string $navigationGroup = 'Manajemen Produk';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Kategori')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', strtoupper(Str::slug($state)))),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(Category::class, 'slug', ignoreRecord: true)
                        ->disabled()
                        ->dehydrated()
                        ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                        ->formatStateUsing(fn($state) => strtoupper($state)),

                    Forms\Components\Select::make('status')
                        ->options([
                            'active' => 'Aktif',
                            'inactive' => 'Tidak Aktif',
                        ])
                        ->default('active')
                        ->required(),

                    // Forms\Components\Textarea::make('description')
                    //     ->label('Deskripsi Kategori')
                    //     ->columnSpanFull(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->description(
                        fn(Category $record): string =>
                        \Illuminate\Support\Str::limit(strip_tags($record->description), 40) ?? 'Tidak ada deskripsi'
                    )
                    ->html()
                    ->tooltip(fn($record): string => strip_tags($record->description))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug/URL')
                    ->color('gray'),

                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status Aktif')
                    ->beforeStateUpdated(function ($record, $state) {
                        if (!$state) {}
                    })
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status' => $state ? 'active' : 'inactive',
                        ]);
                    })
                    ->state(fn($record) => $record->status === 'active')
                    ->onColor('success')
                    ->offColor('danger'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Total Produk')
                    ->counts('products')
                    ->badge()
                    ->color(fn(int $state): string => $state > 0 ? 'info' : 'gray')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
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
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Kategori & Deskripsi'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('printRekapKategori')
                    ->label('Rekap Kategori (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report-categories', [
                            'items' => $records,
                            'title' => 'DATA MASTER KATEGORI PRODUK'
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "Rekap_Kategori_" . now()->format('d-m-Y') . ".pdf");
                    }),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                'active',
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
