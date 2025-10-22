<?php

namespace App\Models;

use Database\Factories\RunFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Run extends Model
{
    /** @use HasFactory<RunFactory> */
    use HasFactory, HasUuids;

    /**
     * The attributes that aren't mass-assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = ['logs'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requested_at'  => 'integer',
            'responded_at'  => 'integer',
            'started_at'    => 'integer',
            'stopped_at'    => 'integer',
            'response_code' => 'integer',
            'memory'        => 'integer',
            'cost'          => 'integer',
            'is_success'    => 'boolean',
        ];
    }

    /**
     * Get the build run that this run used.
     */
    public function build(): BelongsTo
    {
        return $this->belongsTo(Run::class, 'build_id');
    }

    public function getHttpRequest(): ?string
    {
        $path = storage_path('runs/' . $this->id . '/request.log');

        if (! file_exists($path)) {
            return null;
        }

        return file_get_contents($path);
    }

    public function getHttpResponse(): ?string
    {
        $path = storage_path('runs/' . $this->id . '/response.log');

        if (! file_exists($path)) {
            return null;
        }

        return file_get_contents($path);
    }

    /**
     * Get the logs associated with this run.
     *
     * @return array<int, array<string, string>>
     */
    public function getLogsAttribute(): array
    {
        $directory = storage_path('runs/' . $this->id);

        if (! is_dir($directory)) {
            return [];
        }

        $files = array_diff(scandir($directory), ['.', '..']);

        return array_values($files);
    }
}
