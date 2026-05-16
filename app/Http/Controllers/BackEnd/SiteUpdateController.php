<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\View\View;

class SiteUpdateController extends Controller
{
    // Use Laravel public storage directory.
    private const STATUS_FILE = 'storage/app/public/site-updater/status.json';

    public function index(): View
    {
        return view('backEnd.admin.site_update');
    }

    public function check(): JsonResponse
    {
        $syncDetails = $this->detectSyncState();
        if ($syncDetails === null) {
            return response()->json([
                'ok' => false,
                'message' => 'Git branch, remote, or commits could not be detected.',
            ], 422);
        }

        $syncState = $syncDetails['sync_state'];
        $requiresForceReset = in_array($syncState, ['diverged', 'local_ahead_only'], true);
        $updateAvailable = $syncState !== 'up_to_date';

        return response()->json([
            'ok' => true,
            'update_available' => $updateAvailable,
            'remote' => $syncDetails['remote'],
            'branch' => $syncDetails['branch'],
            'local_commit' => $syncDetails['local_commit'],
            'remote_commit' => $syncDetails['remote_commit'],
            'sync_state' => $syncState,
            'requires_force_reset' => $requiresForceReset,
            'message' => $this->syncStateMessage($syncState),
        ]);
    }

    public function run(Request $request): JsonResponse
    {
        $scriptPath = base_path('server_deploy.sh');
        $statusPath = base_path(self::STATUS_FILE);

        if (! is_file($scriptPath)) {
            return response()->json([
                'ok' => false,
                'message' => 'Update script file is missing.',
            ], 422);
        }

        $syncDetails = $this->detectSyncState();
        if ($syncDetails === null) {
            return response()->json([
                'ok' => false,
                'message' => 'Unable to determine sync state before update.',
            ], 422);
        }

        $syncState = $syncDetails['sync_state'];
        $requiresForceReset = in_array($syncState, ['diverged', 'local_ahead_only'], true);
        $forceResetRequested = $request->boolean('force_reset');

        if ($requiresForceReset && ! $forceResetRequested) {
            return response()->json([
                'ok' => false,
                'message' => 'This update requires a hard reset. Confirm reset and try again.',
                'sync_state' => $syncState,
                'requires_force_reset' => true,
            ], 422);
        }

        $existingStatus = $this->readStatus();
        if (($existingStatus['state'] ?? null) === 'running') {
            return response()->json([
                'ok' => false,
                'message' => 'An update is already running.',
            ], 409);
        }

        $statusDir = dirname($statusPath);
        if (! is_dir($statusDir)) {
            if (! @mkdir($statusDir, 0755, true)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Unable to create status directory. Check storage permissions.',
                ], 500);
            }
        }

        $statusPayload = [
            'state' => 'running',
            'message' => 'Update started...',
            'started_at' => now()->toDateTimeString(),
            'sync_state' => $syncState,
            'requires_force_reset' => $requiresForceReset,
            'force_reset_used' => $forceResetRequested,
            'local_commit' => $syncDetails['local_commit'],
            'remote_commit' => $syncDetails['remote_commit'],
        ];

        $written = @file_put_contents($statusPath, json_encode($statusPayload, JSON_PRETTY_PRINT));
        if ($written === false) {
            return response()->json([
                'ok' => false,
                'message' => 'Unable to write status file. Check storage/app/public/site-updater permissions.',
            ], 500);
        }

        $logFile = base_path('storage/logs/site-update.log');
        $command = sprintf('nohup bash %s >> %s 2>&1 &', escapeshellarg($scriptPath), escapeshellarg($logFile));

        // Ensure the background process has a sensible PATH so it can find php, git, composer, etc.
        $currentPath = getenv('PATH') ?: '/usr/bin:/bin';

        Process::path(base_path())
            ->env([
                'SITE_UPDATER_FORCE_RESET' => $forceResetRequested ? '1' : '0',
                'PATH' => $currentPath,
            ])
            ->run($command);

        return response()->json([
            'ok' => true,
            'message' => 'Update started in background.',
            'sync_state' => $syncState,
            'requires_force_reset' => $requiresForceReset,
            'force_reset_used' => $forceResetRequested,
        ]);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'status' => $this->readStatus(),
        ]);
    }

    private function readStatus(): array
    {
        $statusPath = base_path(self::STATUS_FILE);

        if (! is_file($statusPath)) {
            return [
                'state' => 'idle',
                'message' => 'No update has been run yet.',
            ];
        }

        $raw = file_get_contents($statusPath);
        if (! is_string($raw) || $raw === '') {
            return [
                'state' => 'idle',
                'message' => 'No update has been run yet.',
            ];
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [
                'state' => 'idle',
                'message' => 'No update has been run yet.',
            ];
        }

        return $decoded;
    }

    private function detectSyncState(): ?array
    {
        $branch = $this->runCommand('git rev-parse --abbrev-ref HEAD');
        if ($branch === '') {
            return null;
        }

        $remoteList = $this->runCommand('git remote');
        $remoteName = '';
        if ($remoteList !== '') {
            $lines = preg_split('/\R/', $remoteList) ?: [];
            $remoteName = trim((string) ($lines[0] ?? ''));
        }

        if ($remoteName === '') {
            return null;
        }

        Process::path(base_path())->run(sprintf('git fetch %s --prune', escapeshellarg($remoteName)));

        $localCommit = $this->runCommand('git rev-parse HEAD');
        $remoteRef = $remoteName.'/'.$branch;
        $remoteCommit = $this->runCommand(sprintf('git rev-parse %s', escapeshellarg($remoteRef)));
        if ($remoteCommit === '') {
            $remoteCommit = $this->runCommand('git rev-parse @{u}');
            $remoteRef = '@{u}';
        }

        if ($localCommit === '' || $remoteCommit === '') {
            return null;
        }

        $mergeBase = $this->runCommand(sprintf('git merge-base HEAD %s', escapeshellarg($remoteRef)), true);

        $syncState = 'diverged';
        if ($localCommit === $remoteCommit) {
            $syncState = 'up_to_date';
        } elseif ($mergeBase !== '' && $mergeBase === $localCommit) {
            $syncState = 'fast_forward_available';
        } elseif ($mergeBase !== '' && $mergeBase === $remoteCommit) {
            $syncState = 'local_ahead_only';
        }

        return [
            'remote' => $remoteName,
            'branch' => $branch,
            'local_commit' => $localCommit,
            'remote_commit' => $remoteCommit,
            'sync_state' => $syncState,
        ];
    }

    private function runCommand(string $command, bool $allowFailure = false): string
    {
        $result = Process::path(base_path())->run($command);

        if (! $result->successful() && ! $allowFailure) {
            return '';
        }

        return trim($result->output());
    }

    private function syncStateMessage(string $syncState): string
    {
        return match ($syncState) {
            'up_to_date' => 'Already up to date.',
            'fast_forward_available' => 'Update is available.',
            'local_ahead_only' => 'Local commits are ahead of remote. Reset confirmation required.',
            default => 'Branch history diverged from remote. Reset confirmation required.',
        };
    }
}
