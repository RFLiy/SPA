<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Master Penjualan';
    protected static ?string $slug = 'MasterPenjualan';
    protected static ?string $navigationGroup = 'Menejemen Penjualan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_code')
                            ->label('ID Pesanan')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'waiting_payment' => 'Waiting Payment',
                                'paid'            => 'Paid (Sudah Bayar)',
                                'processing'      => 'Processing (Packing)',
                                'shipped'         => 'Shipped (In Delivery)',
                                'delivery'        => 'Delivery',
                                'completed'       => 'Completed (Selesai)',
                                'cancelled'       => 'Cancelled',
                            ])
                            ->live()
                            ->required(),

                        Forms\Components\Placeholder::make('progress_percentage')
                            ->label('Progres Pesanan')
                            ->content(fn($record) => $record ? $record->progressPercentage() . '%' : '-'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Customer & Items Detail')
                    ->description('Data otomatis berdasarkan pesanan user')
                    ->schema([
                        Placeholder::make('customer_name')
                            ->label('Nama Customer')
                            ->content(fn($record) => $record?->user?->name ?? 'Unknown'),

                        Placeholder::make('shipping_option')
                            ->label('Pengiriman')
                            ->content(fn($record) => $record?->shipping_option ?? '-'),

                        Placeholder::make('items_list')
                            ->label('Nama Produk')
                            ->content(function ($record) {
                                if (!$record || !$record->items) return '-';
                                return $record->items->map(function ($item) {
                                    $productName = $item->product?->name ?? 'Produk Tidak Diketahui';
                                    return "{$productName} ({$item->quantity} unit)";
                                })->implode(', ');
                            }),

                        Placeholder::make('total_amount')
                            ->label('Total Bayar')
                            ->content(fn($record) => $record ? 'Rp ' . number_format($record->total, 0, ',', '.') : '-'),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Shipment Details')
                    ->schema([
                        Forms\Components\Select::make('courier_name')
                            ->label('Nama Sopir / Driver')
                            ->placeholder('Pilih Sopir')
                            ->required()
                            ->searchable()
                            ->options(
                                User::whereHas('roles', function ($query) {
                                    $query->where('name', 'Kurir');
                                })->pluck('name', 'name')
                            )
                            ->visible(fn($get) => $get('shipping_option') === 'internal'),

                        Forms\Components\TextInput::make('shipping_reference')
                            ->label('Nomor Surat Jalan')
                            ->placeholder('Contoh: SJ0000001')
                            ->required()
                            ->datalist(function () {
                                return \App\Models\Order::whereNotNull('shipping_reference')
                                    ->pluck('shipping_reference')
                                    ->toArray();
                            })
                            ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                if (!$state && $record) {
                                    $lastOrder = \App\Models\Order::whereNotNull('shipping_reference')
                                        ->orderByRaw('LENGTH(shipping_reference) DESC')
                                        ->orderBy('shipping_reference', 'desc')
                                        ->first();

                                    if ($lastOrder) {
                                        $generatedValue = preg_replace_callback('/\d+/', function ($matches) {
                                            $number = $matches[0];
                                            $length = strlen($number);
                                            $nextNumber = intval($number) + 1;
                                            return str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
                                        }, $lastOrder->shipping_reference);

                                        $component->state($generatedValue);
                                    }
                                }
                            })
                            ->visible(fn($get) => $get('shipping_option') === 'internal'),

                        Forms\Components\DatePicker::make('estimated_arrival')
                            ->label('Estimasi Sampai Lokasi')
                            ->required()
                            ->visible(fn($get) => $get('shipping_option') === 'internal'),
                    ])
                    ->columns(3)
                    ->visible(fn($get) => in_array($get('status'), ['shipped', 'delivered', 'delivery', 'completed'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order_code')
                    ->label('ID Order')
                    ->searchable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_sum_quantity')
                    ->label('Total Qty')
                    ->sum('items', 'quantity'),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('JENIS PRODUK')
                    ->counts('items')
                    ->suffix(' Jenis'),

                Tables\Columns\TextColumn::make('items_sum_quantity')
                    ->label('TOTAL ITEM')
                    ->sum('items', 'quantity')
                    ->suffix(' Pcs'),

                Tables\Columns\TextColumn::make('total')
                    ->label('TOTAL')
                    ->money('idr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'waiting_payment' => 'gray',
                        'paid', 'processing' => 'info',
                        'shipped', 'delivered', 'delivery' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('d M Y')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Pilih Status')
                    ->options([
                        'completed' => 'Completed',
                        'paid' => 'Paid',
                        'shipped' => 'Shipped',
                        'processing' => 'processing',
                        'cancelled' => 'Cancelled',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->actions([
            // Tables\Actions\Action::make('updateStatus')
            //     ->label('Ubah Status')
            //     ->icon('heroicon-o-arrow-path')
            //     ->color('info')
            //     ->form([
            //         Forms\Components\Select::make('status')
            //             ->label('Status Baru')
            //             ->options([
            //                 'waiting_payment' => 'Waiting Payment',
            //                 'paid'            => 'Paid (Sudah Bayar)',
            //                 'processing'      => 'Processing (Packing)',
            //                 'shipped'         => 'Shipped (In Delivery)',
            //                 'delivery'        => 'Delivery',
            //                 'completed'       => 'Completed (Selesai)',
            //                 'cancelled'       => 'Cancelled',
            //             ])
            //             ->required(),
            //     ])
            //     ->action(function (Order $record, array $data): void {
            //         $record->status = $data['status'];
            //         if (in_array($data['status'], ['paid', 'processing', 'shipped', 'delivered', 'delivery', 'completed'])) {
            //             $record->payment_status = 'paid';
            //         }
            //         $record->save();
            //         $customer = $record->user;
            //         if ($customer) {
            //             $customer->notify(new \App\Notifications\OrderStatusNotification($record, $data['status']));
            //         }
            //     })
            // ->successNotificationTitle('Status pesanan berhasil diperbarui & email dikirim!'),

            Tables\Actions\Action::make('updateStatus')
                ->label('Update')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('Status Baru')
                        ->options([
                            'waiting_payment' => 'Waiting Payment',
                            'paid'            => 'Paid (Sudah Bayar)',
                            'processing'      => 'Processing (Packing)',
                            'shipped'         => 'Shipped (In Delivery)',
                            'delivery'        => 'Delivery',
                            'completed'       => 'Completed (Selesai)',
                            'cancelled'       => 'Cancelled',
                        ])
                        ->default(fn(Order $record) => $record->status)
                        ->required(),
                ])
                ->before(function (Order $record, array $data, Tables\Actions\Action $action) {
                    $statusWeights = [
                        'waiting_payment' => 1,
                        'paid'            => 2,
                        'processing'      => 3,
                        'shipped'         => 4,
                        'delivery'        => 4,
                        'completed'       => 5,
                        'cancelled'       => 99,
                    ];

                    $currentStatus = $record->status;
                    $newStatus = $data['status'];
                    if (in_array($currentStatus, ['completed', 'cancelled']) && $newStatus !== $currentStatus) {
                        Notification::make()
                            ->title('Gagal Mengubah Status!')
                            ->body("Pesanan yang telah '" . ucfirst($currentStatus) . "' tidak dapat diubah statusnya lagi.")
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                    if ($newStatus !== 'cancelled') {
                        if ($statusWeights[$newStatus] < $statusWeights[$currentStatus]) {
                            Notification::make()
                                ->title('Gagal Mengubah Status!')
                                ->body("Status tidak boleh mundur kembali ke '" . ucfirst(str_replace('_', ' ', $newStatus)) . "'.")
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }
                })
                ->action(function (Order $record, array $data): void {
                    $newStatus = $data['status'];

                    $record->status = $newStatus;
                    if (in_array($newStatus, ['paid', 'processing', 'shipped', 'delivered', 'delivery', 'completed'])) {
                        $record->payment_status = 'paid';
                    }

                    $record->save();
                    $customer = $record->user;
                    if ($customer) {
                        $customer->notify(new \App\Notifications\OrderStatusNotification($record, $newStatus));
                    }
                    Notification::make()
                        ->title('Status Berhasil Diperbarui')
                        ->body("Status pesanan #{$record->order_code} kini berubah menjadi " . ucfirst($newStatus) . ".")
                        ->success()
                        ->send();
                }),

                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Lengkap Produk'),

                // Tables\Actions\Action::make('printSJ')
                //     ->label('Cetak SJ')
                //     ->icon('heroicon-o-printer')
                //     ->color('success')
                //     ->visible(fn($record) => $record->shipping_reference !== null)
                //     ->action(function ($record) {
                //         $pdf = Pdf::loadView('pdf.surat-jalan-order', ['order' => $record]);
                //         return response()->streamDownload(fn() => print($pdf->output()), "SJ-{$record->order_code}.pdf");
                //     }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('printRekapOrder')
                    ->label('Rekap Order (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rekap-surat-jalan', [
                            'records' => $records,
                            'title' => 'REKAPITULASI ORDER PENJUALAN'
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "Rekap_Order_" . now()->format('Y-m-d') . ".pdf");
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
