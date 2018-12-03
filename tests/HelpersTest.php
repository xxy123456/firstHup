<?php
/**
 * Created by PhpStorm.
 * User: hooklife
 * Date: 2018/12/3
 * Time: 下午3:32
 */
use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase {
    public function testEnv()
    {
        putenv('foo=bar');
        $this->assertEquals('bar', env('foo'));
    }
    public function testEnvWithQuotes()
    {
        putenv('foo="bar"');
        $this->assertEquals('bar', env('foo'));
    }
    public function testEnvTrue()
    {
        putenv('foo=true');
        $this->assertTrue(env('foo'));
        putenv('foo=(true)');
        $this->assertTrue(env('foo'));
    }
    public function testEnvFalse()
    {
        putenv('foo=false');
        $this->assertFalse(env('foo'));
        putenv('foo=(false)');
        $this->assertFalse(env('foo'));
    }
    public function testEnvEmpty()
    {
        putenv('foo=');
        $this->assertEquals('', env('foo'));
        putenv('foo=empty');
        $this->assertEquals('', env('foo'));
        putenv('foo=(empty)');
        $this->assertEquals('', env('foo'));
    }
    public function testEnvNull()
    {
        putenv('foo=null');
        $this->assertEquals('', env('foo'));
        putenv('foo=(null)');
        $this->assertEquals('', env('foo'));
    }
}