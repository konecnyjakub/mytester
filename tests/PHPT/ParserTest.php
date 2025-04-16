<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class Parser
 *
 * @author Jakub Konečný
 */
#[TestSuite("PHPT parser")]
#[Group("phpt")]
final class ParserTest extends TestCase
{
    public function testParse(): void
    {
        $parser = new Parser();

        $sections = $parser->parse(__DIR__ . DIRECTORY_SEPARATOR . "skipped_test.phpt");
        $this->assertSame([
            Parser::SECTION_TEST => "Skipped test",
            Parser::SECTION_SKIPIF => "<?php echo \"skip\"; ?>",
            Parser::SECTION_ENV => [],
            Parser::SECTION_INI => [],
            Parser::SECTION_ARGS => "",
        ], $sections);

        $sections = $parser->parse(__DIR__ . DIRECTORY_SEPARATOR . "test.phpt");
        $this->assertSame([
            Parser::SECTION_TEST => "Test",
            Parser::SECTION_FILE => "<?php" . PHP_EOL . "echo \"test123\";" . PHP_EOL . "?>",
            Parser::SECTION_EXPECT => "test123",
            Parser::SECTION_ENV => [],
            Parser::SECTION_INI => [],
            Parser::SECTION_ARGS => "",
        ], $sections);

        $sections = $parser->parse(__DIR__ . DIRECTORY_SEPARATOR . "test_env.phpt");
        $this->assertSame([
            Parser::SECTION_TEST => "Test env",
            Parser::SECTION_FILE => "<?php echo getenv(\"one\"); ?>",
            Parser::SECTION_EXPECT => "abc",
            Parser::SECTION_ENV => ["one" => "abc", ],
            Parser::SECTION_INI => [],
            Parser::SECTION_ARGS => "",
        ], $sections);

        $sections = $parser->parse(__DIR__ . DIRECTORY_SEPARATOR . "test_args.phpt");
        $this->assertSame([
            Parser::SECTION_TEST => "Test args",
            Parser::SECTION_ARGS => "--one=abc --two def",
            Parser::SECTION_FILE => "<?php var_dump(\$argv[1] === \"--one=abc\" && \$argv[2] === \"--two\" && \$argv[3] === \"def\"); ?>",
            Parser::SECTION_EXPECT => "bool(true)",
            Parser::SECTION_ENV => [],
            Parser::SECTION_INI => [],
        ], $sections);

        $this->assertSame([], $parser->parse(__DIR__ . DIRECTORY_SEPARATOR . "non-existing.phpt"));
    }
}
