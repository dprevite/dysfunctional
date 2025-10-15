<?php

declare(strict_types=1);

class Application
{
    public function run(): void
    {
        echo "Hey baby.\n";
    }
}

$app = new Application();
$app->run();