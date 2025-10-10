<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

test(
    description: 'can parse function yaml',
    closure: function (): void {
        $contents = file_get_contents(
            filename: 'function.yml'
        );

        Yaml::parse($contents);

        echo "YAML parsed successfully.\n";

        expect(true)->toBeTrue();
    });
