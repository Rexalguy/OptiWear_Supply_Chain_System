<?php

namespace App\Filament\Admin\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Admin\Widgets\UserStatsOverview;

class UserInsight extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationGroup = 'User Monitor';

    protected static string $view = 'filament.admin.pages.user-insight';

        protected function getHeaderWidgets(): array
{
    return [
        UserStatsOverview::class,
        
        
    ];
}

        public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                        ->colors([
                            'primary' => 'admin',
                            'success' => 'customer',
                            'warning' => 'vendor',
                            'danger'  => 'manufacturer',
                            // add more roles and colors as needed
                        ])
                    ->sortable(),

                TextColumn::make('tokens')
                    ->label('Tokens')
                    ->sortable(),

                TextColumn::make('gender')
                    ->label('Gender')
                    ->sortable(),

                TextColumn::make('age')
                    ->label('Age')
                    ->state(function ($record) {
                return $record->date_of_birth
                    ? Carbon::parse($record->date_of_birth)->age
                    : 'N/A';
                    }),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return User::query();
    }


public function getTarget(): int
{
    return 100;
}

public function getCurrent(): int
{
    return User::count(); // You can add filters like ->where('role', 'customer') if needed
}

public function getProgressPercentage(): float
{
    return min(($this->getCurrent() / $this->getTarget()) * 100, 100);
}

public function getMilestones(): array
{
    return [10, 25, 50, 75, 100];
}

}
