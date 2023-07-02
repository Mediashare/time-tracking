<?php

namespace Mediashare;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

error_reporting(E_ALL & ~E_DEPRECATED);

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}