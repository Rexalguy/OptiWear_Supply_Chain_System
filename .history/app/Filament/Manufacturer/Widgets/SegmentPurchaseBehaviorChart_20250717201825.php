<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentPurchaseBehaviorChart extends ChartWidget
{
    protected static ?string $heading = 'Purchase Behavior by Customer Segment';

    Internal Server Error

Symfony\Component\ErrorHandler\Error\FatalError
Maximum execution time of 60 seconds exceeded
GET 127.0.0.1:8000
PHP 8.2.12 — Laravel 12.20.0

Expand
vendor frames

C:\xampp\htdocs\OptiWear_Supply_Chain_Supply\vendor\composer\ClassLoader.php
:429
C:\xampp\htdocs\OptiWear_Supply_Chain_Supply\vendor\composer\ClassLoader.php :429
    {
        if ($file = $this->findFile($class)) {
            $includeFile = self::$includeFile;
            $includeFile($file);
 
            return true;
        }
 
        return null;
    }
 
    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|false The path if found, false otherwise
 
Request
GET /manufacturer/segmentation-insights
Headers
host
127.0.0.1:8000
connection
keep-alive
cache-control
max-age=0
sec-ch-ua
"Not)A;Brand";v="8", "Chromium";v="138", "Microsoft Edge";v="138"
sec-ch-ua-mobile
?0
sec-ch-ua-platform
"Windows"
upgrade-insecure-requests
1
user-agent
Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0
accept
text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
sec-fetch-site
same-origin
sec-fetch-mode
navigate
sec-fetch-user
?1
sec-fetch-dest
document
referer
http://127.0.0.1:8000/manufacturer/demand-insights
accept-encoding
gzip, deflate, br, zstd
accept-language
en-GB,en;q=0.9,en-US;q=0.8
cookie
XSRF-TOKEN=eyJpdiI6Im1HUjdxanNuYlZqa3Zhd3NkL2R5bVE9PSIsInZhbHVlIjoiYldzOEdxQ0VBR1QzNUhFUDErd25VU3IrN0FxQ3Z2S01QOC9CT0krSk50RWkyMU9BN0xoMGhHRllnb1lWY1dtME1vdmI0L2JMMEpqYWdIbG0xc2NkS013SGtnOEZMdGk4QUVMWStXam9xUnE1R1ViTHUyTXkyWm52NTU2VUxsWXkiLCJtYWMiOiJjZTRkNjc1N2Q5ZjU3YjVmYmFlOWExYWQzOTdhMTI1ZGU4YWUxMTMwN2RhMjBiNGZmMWEzZjJlNjQwMWU5YzFlIiwidGFnIjoiIn0%3D; laravel_session=eyJpdiI6IkU5UmJQSXV5Um5oRnkzclpyS3NMN0E9PSIsInZhbHVlIjoiZ0hJNGFwZVJ1ZlpqbEoxcm5reVZCeWxPeWNoNXhyREdXcXVDeStsWTl3cUNDZno1MExqTEZjOUJnRlVLV0VPczJpQlNXVzgzdFQ0Q3ZMMy9JN2tCcGdieTJxK2g0V1VKSTVNWURKL2lDV0FuZFVGMms4TlRtcVlneDhXVFNSTEMiLCJtYWMiOiI3ZjNlZWI0N2E4YTcxNGI5ODM3ZTk1ZGRmOWI2OTllYWQyMTQwMzlmMDliYzMyZGVkNGJjNzQ1MDhhNjE4NzJiIiwidGFnIjoiIn0%3D
Body
No body data
Application
Routing
No routing data
Database Queries
No query data
    
    protected function getData(): array
    {
        try {
            // Get total purchases per segment with performance limit
            $segmentData = DB::table('segmentation_results')
                ->select('segment_label', DB::raw('SUM(total_purchased) as total_purchases'))
                ->groupBy('segment_label')
                ->orderBy('total_purchases', 'desc')
                ->limit(10)
                ->get();

            if ($segmentData->isEmpty()) {
                return [
                    'datasets' => [
                        [
                            'label' => 'No Data Available',
                            'data' => [0],
                            'backgroundColor' => 'rgba(156, 163, 175, 0.7)',
                        ],
                    ],
                    'labels' => ['No segmentation data'],
                ];
            }

            $labels = [];
            $data = [];

            foreach ($segmentData as $segment) {
                $labels[] = $segment->segment_label;
                $data[] = (float) $segment->total_purchases;
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Total Purchases',
                        'data' => $data,
                        'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                        'borderColor' => '#36A2EB',
                        'borderWidth' => 1,
                    ],
                ],
                'labels' => $labels,
            ];
        } catch (\Exception $e) {
            return [
                'datasets' => [
                    [
                        'label' => 'Database Error',
                        'data' => [0],
                        'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    ],
                ],
                'labels' => ['Error loading data'],
            ];
        }
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Total Purchases'
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Customer Segments'
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
