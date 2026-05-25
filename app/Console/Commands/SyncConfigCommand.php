<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SyncConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local-system:sync-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync storage policy and company settings from the central server.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting configuration sync from central server...');

        $apiUrl = config('app.central_api_url');
        $token = config('app.central_api_token');
        $companyId = config('app.company_id');

        if (empty($apiUrl) || empty($token) || empty($companyId)) {
            $this->error('Missing central server configuration (API URL, token, or company ID). Please check your .env file.');
            return 1;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'X-Company-ID' => $companyId,
                    'Accept' => 'application/json',
                ])
                ->get($apiUrl . '/local-system/config');

            if ($response->successful()) {
                $data = $response->json();
                
                // Save config locally as JSON
                Storage::disk('local')->put('local_system_config.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                
                $this->info('✅ Configuration successfully synced and saved locally!');
                
                if (isset($data['storage_policy'])) {
                    $policy = $data['storage_policy'];
                    $this->line(" - Storage Use Case: " . ($policy['use_case'] ?? 'N/A'));
                    $this->line(" - Max File Size: " . ($policy['max_file_size_mb'] ?? 'N/A') . " MB");
                    $this->line(" - Allowed Mime Types: " . implode(', ', $policy['allowed_mimes'] ?? ['All']));
                    if (isset($policy['account'])) {
                        $this->line(" - Storage Account: " . ($policy['account']['name'] ?? 'N/A'));
                    }
                }
                
                return 0;
            } else {
                $this->error('Failed to fetch config from central server. Status code: ' . $response->status());
                Log::error('Central config sync failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Exception during configuration sync: ' . $e->getMessage());
            Log::error('Exception during config sync: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
}
