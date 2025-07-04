protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
{
$schedule->command('send:supplier-report')
->weekly()
->at('00:00')
->timezone('Africa/Kampala');

$schedule->command('send:manufacturer-report')
->weekly()
->at('00:00')
->timezone('Africa/Kampala');
}