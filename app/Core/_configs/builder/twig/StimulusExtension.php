<?php
/**
 * @package  StimulusExtension.php
 * @copyright 24.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\_configs\builder\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StimulusExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('stimulus_controller', [$this, 'stimulusController']),
            new TwigFunction('stimulus_target', [$this, 'stimulusTarget']),
            new TwigFunction('stimulus_action', [$this, 'stimulusAction']),
        ];
    }

    public function stimulusController(string $controllerName): string
    {
        return sprintf('data-controller=%s', $controllerName);
    }

    public function stimulusTarget(string $controllerName, string $targetName): string
    {
        return sprintf('data-%s-target=%s', $controllerName, $targetName);
    }

    public function stimulusAction(string $controllerName, string $actionName, string $event = ''): string
    {
        if ($event) {
            return sprintf('data-action=%s->%s#%s', $event, $controllerName, $actionName);
        }
        return sprintf('data-action=%s#%s', $controllerName, $actionName);
    }
}