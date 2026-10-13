<?php

use PHPUnit\Framework\TestSuite;

/**
 * AllTestsTest class
 *
 * This test group will run all MenuBuilder tests.
 */
class AllTestsTest extends TestSuite
{
    /**
     * Suite method, defines tests for this suite.
     *
     * @return CakeTestSuite
     */
    public static function suite(): CakeTestSuite
    {
        $suite = new CakeTestSuite('All Tests');
        $suite->addTestDirectoryRecursive(App::pluginPath('MenuBuilder') . 'tests' . DS . 'TestCase' . DS);

        return $suite;
    }
}
