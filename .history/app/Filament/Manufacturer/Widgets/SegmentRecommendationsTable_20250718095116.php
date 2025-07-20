<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\Widget;

class SegmentRecommendationsTable extends Widget
{
    protected static ?string $heading = 'Segment Recommendations';
    protected int | string | array $columnSpan = 2;
    protected static string $view = 'filament.manufacturer.widgets.segment-recommendations-table';
    
    public function getHeading(): ?string
    {
        return static::$heading;
    }
    
    public function getViewData(): array
    {
        return [
            'recommendations' => [
                [
                    'segment' => 'High-Value Customers',
                    'recommendation' => 'Offer premium products, exclusive deals, and personalized shopping experiences.',
                    'action' => 'Focus on luxury items and VIP services'
                ],
                [
                    'segment' => 'Frequent Buyers',
                    'recommendation' => 'Implement loyalty programs and frequent buyer discounts.',
                    'action' => 'Create subscription services and bulk discounts'
                ],
                [
                    'segment' => 'Price-Sensitive Customers',
                    'recommendation' => 'Promote sales, discounts, and value-for-money products.',
                    'action' => 'Highlight budget-friendly options and clearance items'
                ],
                [
                    'segment' => 'Seasonal Shoppers',
                    'recommendation' => 'Send targeted campaigns during peak seasons and holidays.',
                    'action' => 'Schedule seasonal marketing and inventory planning'
                ],
                [
                    'segment' => 'New Customers',
                    'recommendation' => 'Provide welcome offers and product education materials.',
                    'action' => 'Onboarding programs and first-purchase incentives'
                ],
                [
                    'segment' => 'At-Risk Customers',
                    'recommendation' => 'Re-engagement campaigns and win-back offers.',
                    'action' => 'Personalized outreach and special retention deals'
                ]
            ]
        ];
    }
}
