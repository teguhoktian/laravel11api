<?php

namespace App\Http\Controllers\Settings;

use App\APIResponseBuilder;
use App\Http\Controllers\Controller;
use App\Jobs\CreateBackupJob;
use App\Rules\PathToZip;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Backup\Helpers\Format;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public $activeDisk = '';
    public $disks = [];
    public $files = [];
    public $deletingFile = null;

    /**
     * Show Backup Status
     *
     * @param Request $request
     * @return void
     */

    public function index(Request $request): JsonResponse
    {
        if (!$this->activeDisk and count($this->backupStatuses())) {
            $this->activeDisk = $this->backupStatuses()[0]['disk'];
        }

        $this->disks = collect($this->backupStatuses())
            ->map(function ($backupStatus) {
                return $backupStatus['disk'];
            })
            ->values()
            ->all();

        return APIResponseBuilder::success([
            'disks' => $this->disks,
            'activeDisk' => $request->disk ?: $this->activeDisk,
        ]);
    }

    function backupStatuses(): array
    {
        return Cache::remember('backup-statuses', now()->addSeconds(4), function () {
            return BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitor_backups'))->map(function (BackupDestinationStatus $backupDestinationStatus) {
                return [
                    'name' => $backupDestinationStatus->backupDestination()->backupName(),
                    'disk' => $backupDestinationStatus->backupDestination()->diskName(),
                    'reachable' => $backupDestinationStatus->backupDestination()->isReachable(),
                    'healthy' => $backupDestinationStatus->isHealthy(),
                    'amount' => $backupDestinationStatus->backupDestination()->backups()->count(),
                    'newest' => $backupDestinationStatus->backupDestination()->newestBackup()
                        ? $backupDestinationStatus->backupDestination()->newestBackup()->date()->diffForHumans()
                        : 'No backups present',
                    'usedStorage' => Format::humanReadableSize($backupDestinationStatus->backupDestination()->usedStorage()),
                ];
            })
                ->values()
                ->toArray();
        });
    }

    /**
     * Get Files Function
     *
     * @param string $disk
     * @return void
     */

    public function getFiles(Request $request): array
    {

        if ($request->disk) {
            $this->activeDisk = $request->disk;
        }

        $backupDestination = BackupDestination::create($this->activeDisk, config('backup.backup.name'));

        return $backupDestination
            ->backups()
            ->map(function (Backup $backup) {
                $size = method_exists($backup, 'sizeInBytes') ? $backup->sizeInBytes() : $backup->size();

                return [
                    'path' => $backup->path(),
                    'date' => $backup->date()->format('Y-m-d H:i:s'),
                    'size' => Format::humanReadableSize($size),
                ];
            })
            ->toArray();
    }

    public function createBackup(Request $request): JsonResponse
    {
        dispatch(new CreateBackupJob($request->option));
        return APIResponseBuilder::success([], __("Pencadangan sedang diproses. Silahkan refresh untuk melihat hasil."));
    }

    public function downloadBackup(Request $request)
    {
        $this->validateFilePath($request->path);

        $backupDestination = BackupDestination::create($request->disk, config('backup.backup.name'));

        $backup = $backupDestination->backups()->first(function (Backup $backup) use ($request) {
            return $backup->path() === $request->path;
        });

        if (!$backup) {
            return response('Backup not found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->respondWithBackupStream($backup);
    }

    public function respondWithBackupStream(Backup $backup): StreamedResponse
    {
        $fileName = pathinfo($backup->path(), PATHINFO_BASENAME);
        $size = method_exists($backup, 'sizeInBytes') ? $backup->sizeInBytes() : $backup->size();

        $downloadHeaders = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => 'application/zip',
            'Content-Length' => $size,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'public',
        ];

        return response()->stream(function () use ($backup) {
            $stream = $backup->stream();

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $downloadHeaders);
    }

    protected function validateFilePath(string $filePath)
    {
        try {
            Validator::make(
                ['file' => $filePath],
                [
                    'file' => ['required', new PathToZip],
                ],
                [
                    'file.required' => 'Select a file',
                ]
            )->validate();
        } catch (ValidationException $e) {
            $message = $e->validator->errors()->get('file')[0];
            return APIResponseBuilder::invalidData($message);
        }
    }

    public function deleteFile(Request $request)
    {
        $this->files = $this->getFiles($request);

        $deletingFile = $this->files[$request->index_file];;

        $this->validateFilePath($deletingFile ? $deletingFile['path'] : '');

        $backupDestination = BackupDestination::create($this->activeDisk, config('backup.backup.name'));

        $backupDestination
            ->backups()
            ->first(function (Backup $backup) use ($deletingFile) {
                return $backup->path() === $deletingFile['path'];
            })
            ->delete();

        return APIResponseBuilder::success([], __('File cadangan telah berhasil dihapus.'));
    }
}
