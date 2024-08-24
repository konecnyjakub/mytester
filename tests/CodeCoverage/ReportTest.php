<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\Attributes\TestSuite;
use MyTester\CodeCoverage\Engines\DummyEngine;
use MyTester\TestCase;

/**
 * Test suite for class Report
 *
 * @author Jakub Konečný
 */
#[TestSuite("Code coverage report")]
final class ReportTest extends TestCase
{
    public function testReport(): void
    {
        $report = new Report((new DummyEngine())->collect());

        $this->assertSame(8, $report->linesTotal);
        $this->assertSame(5, $report->linesCovered);
        $this->assertSame(62, $report->coveragePercent);
        $this->assertSame("/var/project/src/", $report->sourcePath);

        $this->assertCount(3, $report->files);
        $file = $report->files[0];
        $this->assertSame("file1.php", $file->name);
        $this->assertSame(3, $file->linesTotal);
        $this->assertSame(2, $file->linesCovered);
        $this->assertSame(66, $file->coveragePercent);
        $file = $report->files[1];
        $this->assertSame("sub1/file2.php", $file->name);
        $this->assertSame(3, $file->linesTotal);
        $this->assertSame(1, $file->linesCovered);
        $this->assertSame(33, $file->coveragePercent);
        $file = $report->files[2];
        $this->assertSame("sub2/file3.php", $file->name);
        $this->assertSame(2, $file->linesTotal);
        $this->assertSame(2, $file->linesCovered);
        $this->assertSame(100, $file->coveragePercent);
    }

    public function testFileClasses(): void
    {
        $basePath = (string) realpath(__DIR__ . "/../../src");
        $data = [
            $basePath . "/TestCase.php" => [],
            $basePath . "/Tester.php" => [],
            $basePath . "/SkipChecker.php" => [],
        ];
        $report = new Report($data);

        $this->assertCount(3, $report->files);
        $file = $report->files[0];
        $this->assertSame("TestCase.php", $file->name);
        $this->assertCount(1, $file->classes);
        $this->assertSame(\MyTester\TestCase::class, $file->classes[0]->getName());
        $file = $report->files[1];
        $this->assertSame("Tester.php", $file->name);
        $this->assertCount(1, $file->classes);
        $this->assertSame(\MyTester\Tester::class, $file->classes[0]->getName());
        $file = $report->files[2];
        $this->assertSame("SkipChecker.php", $file->name);
        $this->assertCount(1, $file->classes);
        $this->assertSame(\MyTester\SkipChecker::class, $file->classes[0]->getName());
    }
}
