<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\UX\Turbo\Twig;

use Twig\Environment;

/**
 * Render turbo stream attributes for a scoped topic.
 *
 * @author Rick Kuipers <rick@levelup-it.com>
 */
interface ScopedTurboStreamListenRendererInterface
{
    /**
     * @param string|object $topic
     */
    public function renderScopedTurboStreamListen(Environment $env, $topic, string $scope): string;
}
