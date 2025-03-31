<?php

namespace App\Filament\Resources\ExpensesResource\Widgets;

use App\Enums\ExpensesType;
use App\Models\Expenses;
use Filament\Support\Colors\Color;
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
            ->map(fn(ExpensesType $type) => Color::rgb($type->getColor()))
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
