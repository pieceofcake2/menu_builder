<?php

use PHPUnit\Framework\TestSuite;

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
        $suite->addTestDirectoryRecursive(App::pluginPath('MenuBuilder') . 'Test' . DS . 'Case' . DS);

        return $suite;
    }
}
