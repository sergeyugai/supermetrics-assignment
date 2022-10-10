<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

/**
 * @class AverageNumberOfPostsPerUserPerMonthCalculator
 * This class calculates average number of posts per user per month.
 *
 * Since we are not sure if calculation is being done on one specific month or over course of several months,
 * the output StatisticsTo might contain several children, one child StatisticsTo object per month.
 */
class AverageNumberOfPostsPerUserPerMonthCalculator extends AbstractCalculator
{
    protected const UNITS = 'posts';

    private array $postsCount = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $monthYearPeriod = $postTo->getDate()->format('m/Y');
        if (! array_key_exists($monthYearPeriod, $this->postsCount)) {
            $this->postsCount[$monthYearPeriod] = [];
        }

        $key = $postTo->getAuthorId();
        $this->postsCount[$monthYearPeriod][$key] = ($this->postsCount[$monthYearPeriod][$key] ?? 0) + 1;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        foreach ($this->postsCount as $splitPeriod => $posts) {
            $totalPosts = array_sum(array_values($posts));
            $numberOfUsers = count($posts);
            $postsPerUser = $totalPosts / $numberOfUsers;
            $average = round($postsPerUser, 2);

            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setSplitPeriod($splitPeriod)
                ->setValue($average)
                ->setUnits(self::UNITS);

            $stats->addChild($child);
        }

        return $stats;
    }
}
