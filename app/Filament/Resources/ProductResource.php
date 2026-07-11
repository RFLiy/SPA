<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageConverterService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Master Produk';
    protected static ?string $navigationGroup = 'Manajemen Produk';
    protected static ?string $slug = 'MasterProduk';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->description('Lengkapi detail informasi produk di bawah ini.')
                    ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state) {
                        $categoryName = '';
                        if ($get('category_id')) {
                            $categoryName = Category::find($get('category_id'))?->name;
                        }
                        $combined = ($categoryName ? $categoryName . '-' : '') . $state;
                        $set('slug', strtoupper(Str::slug($combined)));
                    }),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(Product::class, 'slug', ignoreRecord: true)
                    ->disabled()
                    ->dehydrated()
                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                    ->formatStateUsing(fn($state) => strtoupper($state)),

                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->where('status', 'active')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state) {
                        $productName = $get('name') ?? '';
                        $categoryName = '';

                        if ($state) {
                            $categoryName = Category::find($state)?->name;
                        }
                        $combined = ($categoryName ? $categoryName . '-' : '') . $productName;
                        $set('slug', strtoupper(Str::slug($combined)));
                    }),

                Forms\Components\Toggle::make('status')
                    ->label('Status Produk Aktif')
                    ->helperText('Jika dimatikan, produk tidak akan tampil di web/transaksi.')
                    ->onColor('success')
                    ->offColor('danger')
                    ->dehydrateStateUsing(fn($state) => $state ? 'active' : 'inactive')
                    ->formatStateUsing(fn($state) => $state === 'active')
                    ->default(true)
                    ->required(),

                Forms\Components\Select::make('material_id')
                    ->label('Bahan Baku Utama')
                    ->relationship('material', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('base_price')
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Harga Produk')
                    ->required(),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('stock')
                                ->label('Stok Barang')
                                ->numeric()
                                ->default(0)
                                ->required(),

                            Forms\Components\TextInput::make('unit')
                                ->label('Satuan')
                                ->default('pcs')
                                ->placeholder('pcs, unit, kg, dll')
                                ->required(),
                        ]),

                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi Produk')
                    ->columnSpanFull()
                    ->required(),
                ])->columns(2),

            Forms\Components\FileUpload::make('image')
                ->label('Foto Produk')
                ->image()
                ->disk('s3')
                ->directory('products')
                ->imageEditor()
                ->saveUploadedFileUsing(function ($file) {
                    $converter = app(ImageConverterService::class);
                    $webpContent = $converter->encodeToWebp($file, maxWidth: 1000, quality: 85);

                    $filename = 'products/' . Str::uuid() . '.webp';

                    \Illuminate\Support\Facades\Storage::disk('s3')->put($filename, $webpContent);

                    return $filename;
                })
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->disk('s3')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Harga')
                    ->money('idr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->suffix(fn($record) => " " . $record->unit)
                    ->sortable(),

                Tables\Columns\TextColumn::make('base_material')
                    ->label('Material')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status Produk')
                    ->updateStateUsing(function ($record, $state) {
                        $record->update([
                            'status' => $state ? 'active' : 'inactive',
                        ]);
                    })
                    ->state(fn($record) => $record->status === 'active')
                    ->onColor('success')
                    ->disabled(fn(Product $record) => $record->category?->status === 'inactive'),
            ])
            ->recordClasses(fn(Product $record) => match ($record->status) {
                'inactive' => 'opacity-50 italic bg-gray-50',
                default => null,
            })
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Produk Berhasil Dihapus!'),
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Lengkap Produk'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('printRekapProduk')
                    ->label('Rekap Produk (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report-products', [
                            'items' => $records,
                            'title' => 'LAPORAN STOK & HARGA PRODUK'
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "Rekap_Produk_" . now()->format('d-m-Y') . ".pdf");
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
