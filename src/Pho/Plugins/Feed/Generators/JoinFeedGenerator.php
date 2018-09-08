<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Plugins\Feed\Generators;

use Pho\Kernel\Foundation\ParticleInterface;

class JoinFeedGenerator {

    public static function process(ParticleInterface $member, ParticleInterface $group): string
    {
        $feed = "[%s|%s] joined a new group called [%s|%s]";
        $feed = sprintf($feed, $member->getUsername(), (string) $member->id(), $group->getTitle(), (string) $group->id());
        return $feed;
    }
}