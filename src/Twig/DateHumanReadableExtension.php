<?php
namespace App\Twig;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateHumanReadableExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('human_readable', [$this, 'humanReadable']),
        ];
    }

    public function humanReadable($date): string
    {
        Carbon::setLocale('fr');
        return Carbon::instance($date)->diffForHumans();
    }
}