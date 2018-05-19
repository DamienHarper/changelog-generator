<?php

declare(strict_types=1);

namespace ChangelogGenerator\Tests;

use ChangelogGenerator\Issue;
use ChangelogGenerator\IssueFactory;
use ChangelogGenerator\IssueFetcher;
use ChangelogGenerator\IssueRepository;
use PHPUnit\Framework\TestCase;

final class IssueRepositoryTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|IssueFetcher */
    private $issueFetcher;

    /** @var \PHPUnit_Framework_MockObject_MockObject|IssueFactory */
    private $issueFactory;

    /** @var IssueRepository */
    private $issueRepository;

    public function testGetMilestoneIssues() : void
    {
        $this->issueFetcher->expects($this->once())
            ->method('fetchMilestoneIssues')
            ->with('jwage', 'changelog-generator', '1.0')
            ->willReturn([
                [
                    'number' => 1,
                    'title' => 'Issue #1',
                    'html_url' => 'https://github.com/jwage/changelog-generator/issue/1',
                    'user' => ['login' => 'jwage'],
                    'labels' => [['name' => 'Enhancement']],
                ],
                [
                    'number' => 2,
                    'title' => '[Bug] Issue #2',
                    'html_url' => 'https://github.com/jwage/changelog-generator/issue/2',
                    'user' => ['login' => 'jwage'],
                    'labels' => [['name' => 'Bug']],
                ],
            ]);

        $issue1 = $this->createMock(Issue::class);
        $issue2 = $this->createMock(Issue::class);

        $this->issueFactory->expects($this->at(0))
            ->method('create')
            ->with(1, 'Issue #1', 'https://github.com/jwage/changelog-generator/issue/1', 'jwage', ['Enhancement'])
            ->willReturn($issue1);

        $this->issueFactory->expects($this->at(1))
            ->method('create')
            ->with(2, '&#91;Bug&#92; Issue #2', 'https://github.com/jwage/changelog-generator/issue/2', 'jwage', ['Bug'])
            ->willReturn($issue2);

        $issues = $this->issueRepository->getMilestoneIssues('jwage', 'changelog-generator', '1.0');

        self::assertCount(2, $issues);
        self::assertSame($issue1, $issues[1]);
        self::assertSame($issue2, $issues[2]);
    }

    protected function setUp() : void
    {
        $this->issueFetcher = $this->createMock(IssueFetcher::class);
        $this->issueFactory = $this->createMock(IssueFactory::class);

        $this->issueRepository = new IssueRepository(
            $this->issueFetcher,
            $this->issueFactory
        );
    }
}