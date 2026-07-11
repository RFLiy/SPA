<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanBualananResource\Pages;
use App\Filament\Resources\LaporanBualananResource\RelationManagers;
use App\Models\LaporanBualanan;
use App\Models\Order;
use App\Models\OrderLaporan;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Notifications\Notification;

class LaporanBualananResource extends Resource
{
    protected static ?string $model = OrderLaporan::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $slug = 'Laporan-Penjualan';
    protected static ?string $navigationBadgeTooltip = 'Total Penjualan';
    protected static ?string $navigationGroup = 'Menejemen Laporan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return 'Laporan Penjualan';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['orderItems.product'])
            ->whereIn('status', ['completed', 'paid', 'shipped' , 'cancelled']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('order_code')
                            ->label('ID Pesanan'),

                        Forms\Components\TextInput::make('created_at')
                            ->label('Tanggal Transaksi')
                            ->afterStateHydrated(fn($component, $state) => $component->state(\Carbon\Carbon::parse($state)
                            ->format('d M Y H:i'))),

                        Forms\Components\TextInput::make('status')
                            ->label('Status Terakhir'),

                        Forms\Components\TextInput::make('total')
                            ->label('Total Pembayaran')
                            ->numeric()
                            ->prefix('Rp'),
                    ])->columns(2),

                    Forms\Components\Repeater::make('orderItems')
                        ->relationship()
                        ->schema([
                    Forms\Components\TextInput::make('product_id')
                        ->label('Produk')
                        ->formatStateUsing(fn($state) => \App\Models\Product::find($state)?->name ?? 'Produk Terhapus'),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Jumlah'),

                    Forms\Components\TextInput::make('price')
                        ->label('Harga Satuan')
                        ->prefix('Rp'),
                        ])
                    ->columns(3)
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ])->disabled();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_code')
                    ->label('ID Pesanan')
                    ->searchable(),

                // Tables\Columns\TextColumn::make('user.name')
                //     ->label('Customer')
                //     ->searchable()
                //     ->sortable(),

                Tables\Columns\TextColumn::make('orderItems.product.name')
                    ->label('Daftar Produk')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->default('Data Produk Hilang')
                    ->searchable(),

                Tables\Columns\TextColumn::make('orderItems')
                    ->label('Produk (Qty)')
                    ->badge()
                    ->color('info')
                    ->state(function (OrderLaporan $record) {
                        return $record->orderItems
                            ->filter(fn($item) => $item->product !== null)
                            ->map(function ($item) {
                                return "({$item->quantity} pcs)";
                            })
                            ->implode("\n");
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total Penjualan')
                    ->money('idr')
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Penjualan (Completed)')
                            ->query(fn($query) => $query->where('status', 'completed'))
                            ->formatStateUsing(fn($state) => 'Rp. ' . number_format($state, 0, ',', '.')),


                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'paid' => 'warning',
                        'shipped', => 'info',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->paginated(false)

            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['dari_tanggal'] && $data['sampai_tanggal']) {
                            if ($data['sampai_tanggal'] < $data['dari_tanggal']) {
                                Notification::make()
                                    ->id('rentang-tanggal-salah')
                                    ->title('Rentang Tanggal Salah')
                                    ->body('"Tanggal ahkir" tidak boleh lebih kecil dari "Tanggal Mulai"!')
                                    ->danger()
                                    ->send();
                        return $query->whereRaw('1 = 0');
                            }
                        }
                            return $query
                                ->when($data['dari_tanggal'], fn($q) => $q->whereDate('created_at', '>=', $data['dari_tanggal']))
                                ->when($data['sampai_tanggal'], fn($q) => $q->whereDate('created_at', '<=', $data['sampai_tanggal']));
                        }),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Pilih Status')
                    ->options([
                        'completed' => 'Completed',
                        'paid' => 'Paid',
                        'shipped' => 'Shipped',
                        'cancelled' => 'Cancelled',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Lengkap'),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->before(function ($livewire, \Filament\Tables\Actions\Action $action) {
                        $filterData = $livewire->tableFilters['created_at'] ?? [];
                        $dariTanggal = $filterData['dari_tanggal'] ?? null;
                        $sampaiTanggal = $filterData['sampai_tanggal'] ?? null;
                        if (blank($dariTanggal) || blank($sampaiTanggal)) {
                            Notification::make()
                                ->title('Gagal Export Data!')
                                ->body('Anda harus memilih filter "Dari Tanggal" dan "Sampai Tanggal" terlebih dahulu sebelum melakukan export laporan.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                        if ($sampaiTanggal < $dariTanggal) {
                            Notification::make()
                                ->title('Gagal Export Data!')
                                ->body('Rentang tanggal salah! "Tanggal Akhir" tidak boleh lebih kecil dari "Tanggal Mulai".')
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->label('Export Excel (Terpilih)')
                        ->color('success'),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pesanan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('order_code')
                        ->label('ID Pesnaan'),
                        TextEntry::make('created_at')
                        ->label('Tanggal Order')
                        ->date('d/m/Y H:i'),
                        TextEntry::make('status')
                        ->label('Status Pesanan')
                        ->badge(),
                        TextEntry::make('payment_status')
                        ->label('Status Pembayaran')
                        ->badge(),
                    ]),
                Section::make('Data Customer')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                        ->label('Nama Pelanggan'),
                        TextEntry::make('user.email')
                        ->label('Email'),
                        TextEntry::make('user.no_tlp')
                        ->label('No. Telepon / WA'),
                        TextEntry::make('shipping_address')
                        ->label('Alamat Pengiriman'),
                    ]),

                Section::make('Detail Barang Yang Dibeli')
                    ->schema([
                        RepeatableEntry::make('orderItems')
                        ->label('Pesanan')
                            ->schema([
                                TextEntry::make('product.name')
                                ->label('Nama Barang'),
                                TextEntry::make('quantity')
                                ->label('Qty (Pcs)'),
                                TextEntry::make('price')
                                ->label('Harga Satuan')
                                ->money('IDR'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanBualanans::route('/'),
            'create' => Pages\CreateLaporanBualanan::route('/create'),
            'edit' => Pages\EditLaporanBualanan::route('/{record}/edit'),
        ];
    }
}
