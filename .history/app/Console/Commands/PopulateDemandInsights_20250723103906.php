<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PopulateDemandInsights extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'insights:populate-demand';

    /**
     * The console command description.
     */
    protected $description = 'Populate demand insights by running the Python demand prediction script';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting demand insights population...');
        
        try {
            // Change to the ML/python_files directory
            $workingDirectory = base_path('ML/python_files');
            
            // Verify the script exists
            $scriptPath = $workingDirectory . DIRECTORY_SEPARATOR . 'generate_realistic_demand_predictions.py';
            if (!file_exists($scriptPath)) {
                $this->error("❌ Python script not found: {$scriptPath}");
                return 1;
            }
            
            // Verify the datasets directory exists (for potential future dependencies)
            $datasetsPath = base_path('ML/datasets');
            if (!is_dir($datasetsPath)) {
                $this->warn("⚠️ Datasets directory not found: {$datasetsPath}");
            }
            
            $this->info("📂 Working directory: {$workingDirectory}");
            $this->info("🐍 Executing Python script...");
            
            // Create and run the process
            $process = new Process(['python', 'generate_realistic_demand_predictions.py'], $workingDirectory);
            $process->setTimeout(300); // 5 minutes timeout
            
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    $this->error('ERROR: ' . $buffer);
                } else {
                    $this->line($buffer);
                }
            });
            
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            
            $this->info('✅ Demand insights populated successfully!');
            $this->info('📊 Charts should now display updated data.');
            
            return 0;
            
        } catch (ProcessFailedException $exception) {
            $this->error('❌ Python script execution failed:');
            $this->error($exception->getMessage());
            return 1;
            
        } catch (\Exception $exception) {
            $this->error('❌ An error occurred:');
            $this->error($exception->getMessage());
            return 1;
        }
    }
}
