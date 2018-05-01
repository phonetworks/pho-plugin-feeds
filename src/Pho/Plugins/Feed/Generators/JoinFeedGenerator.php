<?php

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