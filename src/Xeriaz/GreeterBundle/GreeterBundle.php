<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle;

use App\Xeriaz\GreeterBundle\DependencyInjection\GreeterExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GreeterBundle extends Bundle
{
    /**
     * @return GreeterExtension
     */
    public function getContainerExtension(): GreeterExtension
    {
        return new GreeterExtension();
    }
}
