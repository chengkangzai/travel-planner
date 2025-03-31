<?php

namespace App\Filament\Resources\ExpensesResource\Widgets;

use App\Enums\ExpensesType;
use App\Models\Expenses;
use Filament\Widgets\ChartWidget;

class ExpensesByTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $data = Expenses::query()
            ->selectRaw('type, SUM(amount) as total')
            ->orderBy('total', 'desc')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                // Get the label for this expense type
                $type = $item->type;
                $label = $type->getLabel();

                return [$label => $item->total];
            })
            ->toArray();

        // Get all the colors for the chart
        $colors = collect(ExpensesType::cases())
            ->map(function ($type) {
                return match ($type->getColor()) {
                    'gray' => '#6B7280',
                    'purple' => '#8B5CF6',
                    'success' => '#10B981',
                    'danger' => '#EF4444',
                    'info' => '#3B82F6',
                    'warning' => '#F59E0B',
                    'primary' => '#3B82F6',
                    default => '#6B7280',
                };
            })
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Expenses by Type',
                    'data' => array_values($data),
                    'backgroundColor' => array_values($colors),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
