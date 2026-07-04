<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BahanBakuResource\Pages;
use App\Filament\Resources\BahanBakuResource\RelationManagers;
use App\Models\BahanBaku;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BahanBakuResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static ?string $navigationIcon = 'heroicon-c-clipboard-document-list';
    protected static ?string $navigationLabel = 'Master Bahan Baku';
    protected static ?string $navigationGroup = 'Manajemen Produk';
    protected static ?string $slug = 'MasterBahanBaku';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Informasi Bahan Baku')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Bahan')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($set, $state) => $set('slug', strtoupper(Str::slug($state)))),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->disabled()
                        ->dehydrated()
                        ->extraInputAttributes(['style' => 'text-transform: uppercase']),

                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi Bahan Baku')
                        ->columnSpanFull(),
                ])->columns(2),
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
                    ->label('Nama Bahan')
                    ->description(
                        fn(Material $record): string =>
                        \Illuminate\Support\Str::limit(strip_tags($record->description), 40) ?? 'Tidak ada deskripsi'
                    )
                    ->html()
                    ->tooltip(fn($record): string => strip_tags($record->description))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Kategori & Deskripsi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('printRekapBahanBaku')
                    ->label('Rekap Bahan Baku (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report-materials', [
                            'items' => $records,
                            'title' => 'DATA MASTER BAHAN BAKU PRODUKSI'
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "Rekap_Bahan_Baku_" . now()->format('d-m-Y') . ".pdf");
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
            'index' => Pages\ListBahanBakus::route('/'),
            'create' => Pages\CreateBahanBaku::route('/create'),
            'edit' => Pages\EditBahanBaku::route('/{record}/edit'),
        ];
    }
}
