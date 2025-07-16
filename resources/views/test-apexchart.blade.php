<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test ApexChart</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        #chart {
            max-width: 900px;
            margin: 40px auto;
        }
    </style>
</head>

<body>
    <div id="chart"></div>
    <script>
        // Example data, replace with your actual data if needed
        var options = {
            chart: {
                type: 'line',
                height: 500,
                toolbar: { show: true }
            },
            series: [
                // Example: { name: 'Category 1', data: [10, 20, 30] },
                // Fill with your actual $series data
            ],
            xaxis: {
                categories: [], // Fill with your actual $dates data
                title: {
                    text: 'Date',
                    style: {
                        fontWeight: 600,
                        fontSize: '14px',
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Predicted Demand',
                    style: {
                        fontWeight: 600,
                        fontSize: '14px',
                    }
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
</body>

</html>