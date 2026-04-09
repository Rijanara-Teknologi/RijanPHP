<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for Bug #12: Missing PSR-3 log helper functions.
 *
 * Framework only had logger(), log_info(), log_error(), log_warning().
 * Missing: log_debug(), log_notice(), log_critical(), log_alert(),
 *          log_emergency(), log_request(), log_query().
 *
 * These tests verify the functions exist and are callable with the correct
 * signatures, without requiring a real log channel (we stub LogManager).
 */
class LogHelpersTest extends TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../Core/Helpers/Log.php';
    }

    // -------------------------------------------------------------------------
    // All PSR-3 helper functions must exist
    // -------------------------------------------------------------------------

    public function testLogDebugFunctionExists()
    {
        $this->assertTrue(function_exists('log_debug'), 'log_debug() must be defined');
    }

    public function testLogNoticeFunctionExists()
    {
        $this->assertTrue(function_exists('log_notice'), 'log_notice() must be defined');
    }

    public function testLogCriticalFunctionExists()
    {
        $this->assertTrue(function_exists('log_critical'), 'log_critical() must be defined');
    }

    public function testLogAlertFunctionExists()
    {
        $this->assertTrue(function_exists('log_alert'), 'log_alert() must be defined');
    }

    public function testLogEmergencyFunctionExists()
    {
        $this->assertTrue(function_exists('log_emergency'), 'log_emergency() must be defined');
    }

    // -------------------------------------------------------------------------
    // Specialty helpers must exist
    // -------------------------------------------------------------------------

    public function testLogRequestFunctionExists()
    {
        $this->assertTrue(function_exists('log_request'), 'log_request() must be defined');
    }

    public function testLogQueryFunctionExists()
    {
        $this->assertTrue(function_exists('log_query'), 'log_query() must be defined');
    }

    // -------------------------------------------------------------------------
    // Existing helpers remain present (regression check)
    // -------------------------------------------------------------------------

    public function testLoggerFunctionExists()
    {
        $this->assertTrue(function_exists('logger'), 'logger() must still be defined');
    }

    public function testLogInfoFunctionExists()
    {
        $this->assertTrue(function_exists('log_info'), 'log_info() must still be defined');
    }

    public function testLogErrorFunctionExists()
    {
        $this->assertTrue(function_exists('log_error'), 'log_error() must still be defined');
    }

    public function testLogWarningFunctionExists()
    {
        $this->assertTrue(function_exists('log_warning'), 'log_warning() must still be defined');
    }

    // -------------------------------------------------------------------------
    // Source-level verification: helpers forward to correct PSR-3 levels
    // -------------------------------------------------------------------------

    public function testLogDebugForwardsToDebugLevel()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Helpers/Log.php');
        $this->assertStringContainsString('->debug(', $source);
    }

    public function testLogNoticeForwardsToNoticeLevel()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Helpers/Log.php');
        $this->assertStringContainsString('->notice(', $source);
    }

    public function testLogCriticalForwardsToCriticalLevel()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Helpers/Log.php');
        $this->assertStringContainsString('->critical(', $source);
    }

    public function testLogAlertForwardsToAlertLevel()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Helpers/Log.php');
        $this->assertStringContainsString('->alert(', $source);
    }

    public function testLogEmergencyForwardsToEmergencyLevel()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Helpers/Log.php');
        $this->assertStringContainsString('->emergency(', $source);
    }

    public function testLogRequestForwardsToInfo()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Helpers/Log.php');
        // log_request() should log at info level with HTTP verb and URL in the message
        $this->assertStringContainsString('->info(', $source);
        $this->assertStringContainsString('"HTTP {$method} {$url}"', $source);
    }

    public function testLogRequestFunctionSignatureHasFourParams()
    {
        $ref = new \ReflectionFunction('log_request');
        $params = $ref->getParameters();

        $this->assertCount(4, $params);
        $this->assertSame('method', $params[0]->getName());
        $this->assertSame('url', $params[1]->getName());
        $this->assertSame('ip', $params[2]->getName());
        $this->assertSame('status', $params[3]->getName());
        $this->assertTrue($params[3]->isOptional(), 'status param must be optional');
    }

    public function testLogQueryFunctionSignatureHasThreeParams()
    {
        $ref = new \ReflectionFunction('log_query');
        $params = $ref->getParameters();

        $this->assertCount(3, $params);
        $this->assertSame('sql', $params[0]->getName());
        $this->assertSame('bindings', $params[1]->getName());
        $this->assertSame('time', $params[2]->getName());
        $this->assertTrue($params[1]->isOptional(), 'bindings param must be optional');
        $this->assertTrue($params[2]->isOptional(), 'time param must be optional');
    }
}
