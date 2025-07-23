<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PopulateSegmentationInsights extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'insights:populate-segmentation';

    /**
     * The console command description.
     */
    protected $description = 'Populate segmentation insights by running the Python segmentation script';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting segmentation insights population...');
        
        try {
            // Change to the ML/python_files directory
            $workingDirectory = base_path('ML/python_files');
            
            // Verify the script exists
            $scriptPath = $workingDirectory . DIRECTORY_SEPARATOR . 'segmentation.py';
            if (!file_exists($scriptPath)) {
                $this->error("❌ Python script not found: {$scriptPath}");
                return 1;
            }
            
            // Verify the required dataset exists
            $datasetPath = base_path('ML/datasets/segmentation_dataset_expanded.csv');
            if (!file_exists($datasetPath)) {
                $this->error("❌ Required dataset not found: {$datasetPath}");
                $this->error("The segmentation script requires the CSV dataset to function.");
                return 1;
            }
            
            $this->info("📂 Working directory: {$workingDirectory}");
            $this->info("📊 Dataset found: segmentation_dataset_expanded.csv");
            $this->info("🐍 Executing Python script...");
            
            // Create and run the process
            $process = new Process(['python', 'segmentation.py'], $workingDirectory);
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
            
            $this->info('✅ Segmentation insights populated successfully!');
            $this->info('📊 Charts should now display updated customer segment data.');
            
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
