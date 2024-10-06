<?php

namespace App\FrontEngines;

interface Engine
{
    public function run(string $script): string;

    public function getDispatchHandler(): string;
}
