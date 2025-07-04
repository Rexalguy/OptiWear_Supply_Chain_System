// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
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