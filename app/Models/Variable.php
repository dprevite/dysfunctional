<?php

namespace App\Models;

use Database\Factories\VariableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    /** @use HasFactory<VariableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'path',
        'name',
        'value',
        'is_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_secret' => 'boolean',
        ];
    }

    public static function findByMatchingPath(string $path): array
    {
        $variables = self::where('path', $path)
            ->orderBy('type', 'asc')
            ->get();

        $result = [
            'secrets' => [],
            'variables' => [],
        ];

        $processedNames = [];

        foreach ($variables as $variable) {
            // Skip if we've already processed this name with higher priority
            if (isset($processedNames[$variable->name])) {
                continue;
            }

            // Mark this name as processed
            $processedNames[$variable->name] = true;

            if ($variable->is_secret) {
                $result['secrets'][$variable->name] = $variable->value;
            } else {
                $result['variables'][$variable->name] = $variable->value;
            }
        }

        return $result;
    }
}
