<?php

declare(strict_types = 1);

namespace Tests\unit\Statistics;

use DateTime;
use PHPUnit\Framework\TestCase;
use SocialPost\Hydrator\FictionalPostHydrator;
use SocialPost\Hydrator\SocialPostHydratorInterface;
use Statistics\Calculator\AverageNumberOfPostsPerUserPerMonthCalculator;
use Statistics\Dto\ParamsTo;
use Statistics\Dto\StatisticsTo;
use Statistics\Enum\StatsEnum;

class AverageNumberOfPostsPerUserPerMonthCalculatorTest extends TestCase
{
    protected const JSON_SOURCE = __DIR__ . '/../../data/social-posts-response.json';
    protected const JSON_SOURCE_AVERAGE_POSTS_PER_USER = 1.0;
    protected const JSON_SOURCE_POSTS_MONTH = '08/2018';

    protected SocialPostHydratorInterface $postHydrator;

    /**
     * Main method that tests AverageNumberOfPostsPerUserPerMonth for a given array of months and posts.
     *
     * @dataProvider providePostDataFromJSONFile
     * @dataProvider providePostData
     * @param float $expectedAverage
     * @param array $months
     * @param array $posts
     * @return void
     */
    public function testThatAverageNumberCalculationIsPerformedCorrectly(float $expectedAverage, array $months, array $posts): void
    {
        $hydratedPosts = $this->getHydratedPostsFromArray($posts);

        $calculator = new AverageNumberOfPostsPerUserPerMonthCalculator();
        $calculator->setParameters($this->createNonLimitingParams());
        foreach ($hydratedPosts as $post) {
            $calculator->accumulateData($post);
        }
        $statistics = $calculator->calculate();

        $this->assertCount(count($months), $statistics->getChildren());
        foreach ($months as $month) {
            $statThatMonth = $this->findChildStatisticsForMonth($statistics, $month);
            $this->assertNotNull($statThatMonth);
            $this->assertEqualsWithDelta($expectedAverage, $statThatMonth->getValue(), 0.001);
        }
    }

    /**
     * Provide post data
     * Format is array of test cases:
     * ['test case name' => [average posts, array of months in m/y format, array of post objects]]
     *
     * @return array[]
     */
    public function providePostData(): array
    {
        return require __DIR__ . '/../fixtures/social-post-fixtures.php';
    }

    /**
     * Provide post data from JSON file in tests/data
     *
     * @return array[]
     */
    public function providePostDataFromJSONFile(): array
    {
        $socialPostsResponseFile = file_get_contents(self::JSON_SOURCE);
        $socialPostsResponse = json_decode($socialPostsResponseFile, true);

        return [
            'august responses' => [
                self::JSON_SOURCE_AVERAGE_POSTS_PER_USER,
                [self::JSON_SOURCE_POSTS_MONTH],
                $socialPostsResponse['data']['posts'],
            ]
        ];
    }

    /**
     * Helper function to hydrate posts provided as array
     *
     * @param array $posts
     * @return array
     */
    protected function getHydratedPostsFromArray(array $posts): array
    {
        return array_map(fn($post) => $this->getPostHydrator()->hydrate($post), $posts);
    }

    /**
     * Factory method to create posts hydrator
     *
     * @return SocialPostHydratorInterface
     */
    protected function getPostHydrator(): SocialPostHydratorInterface
    {
        return new FictionalPostHydrator();
    }

    /**
     * This method creates ParamsTo that would allow to input ANY months into our stats calculator
     *
     * Since this is a testing scenario, we are putting no assumption on what the stat period is, effectively allowing
     * for  posts to span over several months.
     *
     * @return ParamsTo
     *
     */
    protected function createNonLimitingParams(): ParamsTo
    {
        $startDate  = (new DateTime())->modify('-10 years');
        $endDate    = (new DateTime())->modify('+10 years');

        return (new ParamsTo())
            ->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER)
            ->setStartDate($startDate)
            ->setEndDate($endDate);
    }

    /**
     * Helps find a StatisticsTo for a specific month in all of AverageNumberOfPostsPerUserPerMonth Statistics
     *
     * @param StatisticsTo $statisticsTo
     * @param string $month
     * @return StatisticsTo|null
     */
    protected function findChildStatisticsForMonth(StatisticsTo $statisticsTo, string $month): ?StatisticsTo
    {
        $children = $statisticsTo->getChildren();
        foreach ($children as $child) {
            if ($child->getSplitPeriod() === $month) {
                return $child;
            }
        }
        return null;
    }
}
