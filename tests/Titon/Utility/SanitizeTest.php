<?php
namespace Titon\Utility;

use Titon\Test\TestCase;

class SanitizeTest extends TestCase {

    public function testEmail() {
        $this->assertEquals('email@domain.com', Sanitize::email('em<a>il@domain.com'));
        $this->assertEquals('email+tag@domain.com', Sanitize::email('email+t(a)g@domain.com'));
        $this->assertEquals('email+tag@domain.com', Sanitize::email('em"ail+t(a)g@domain.com'));
    }

    public function testEscape() {
        $this->assertEquals('"Double" quotes', Sanitize::escape('"Double" quotes', array('flags' => ENT_NOQUOTES)));
        $this->assertEquals('&quot;Double&quot; quotes', Sanitize::escape('"Double" quotes', array('flags' => ENT_COMPAT)));
        $this->assertEquals('&quot;Double&quot; quotes', Sanitize::escape('"Double" quotes', array('flags' => ENT_QUOTES)));

        $this->assertEquals("'Single' quotes", Sanitize::escape("'Single' quotes", array('flags' => ENT_NOQUOTES)));
        $this->assertEquals("'Single' quotes", Sanitize::escape("'Single' quotes", array('flags' => ENT_COMPAT)));
        $this->assertEquals("&#039;Single&#039; quotes", Sanitize::escape("'Single' quotes", array('flags' => ENT_QUOTES)));

        $this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', array('flags' => ENT_NOQUOTES)));
        $this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', array('flags' => ENT_COMPAT)));
        $this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', array('flags' => ENT_QUOTES)));

        $this->assertEquals('&quot;Double&quot; quotes', Sanitize::escape('"Double" quotes', array('flags' => ENT_QUOTES | ENT_HTML5)));
        $this->assertEquals('&quot;Double&quot; quotes', Sanitize::escape('"Double" quotes', array('flags' => ENT_QUOTES | ENT_XHTML)));
        $this->assertEquals("&apos;Single&apos; quotes", Sanitize::escape("'Single' quotes", array('flags' => ENT_QUOTES | ENT_HTML5)));
        $this->assertEquals("&#039;Single&#039; quotes", Sanitize::escape("'Single' quotes", array('flags' => ENT_QUOTES | ENT_XHTML)));
        $this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', array('flags' => ENT_QUOTES | ENT_HTML5)));
        $this->assertEquals('&lt;Html&gt; tags', Sanitize::escape('<Html> tags', array('flags' => ENT_QUOTES | ENT_XHTML)));
    }

    public function testFloat() {
        $this->assertEquals(100.25, Sanitize::float('1array(0)0.25'));
        $this->assertEquals(-125.55, Sanitize::float('-abc125.55'));
        $this->assertEquals(1203.11, Sanitize::float('+1203.11'));
    }

    public function testHtml() {
        $this->assertEquals('String with b &amp; i tags.', Sanitize::html('String <b>with</b> b & i <i>tags</i>.'));
        $this->assertEquals('String &lt;b&gt;with&lt;/b&gt; b &amp; i &lt;i&gt;tags&lt;/i&gt;.', Sanitize::html('String <b>with</b> b & i <i>tags</i>.', array('strip' => false)));
        $this->assertEquals('String &lt;b&gt;with&lt;/b&gt; b &amp; i tags.', Sanitize::html('String <b>with</b> b & i <i>tags</i>.', array('whitelist' => '<b>')));
        $this->assertEquals('String with b &amp;amp; i tags.', Sanitize::html('String <b>with</b> b &amp; i <i>tags</i>.', array('double' => true)));
    }

    public function testInteger() {
        $this->assertEquals(1292932, Sanitize::integer('129sdja2932'));
        $this->assertEquals(-1275452, Sanitize::integer('-12,754.52'));
        $this->assertEquals(18840, Sanitize::integer('+18#840'));
    }

    public function testNewlines() {
        $this->assertEquals("Testing\rCarriage\rReturns", Sanitize::newlines("Testing\rCarriage\r\rReturns"));
        $this->assertEquals("Testing\r\rCarriage\rReturns", Sanitize::newlines("Testing\r\rCarriage\r\r\rReturns", array('limit' => 3)));
        $this->assertEquals("TestingCarriageReturns", Sanitize::newlines("Testing\r\rCarriage\r\r\rReturns", array('limit' => 0)));

        $this->assertEquals("Testing\nLine\nFeeds", Sanitize::newlines("Testing\nLine\n\nFeeds"));
        $this->assertEquals("Testing\nLine\n\nFeeds", Sanitize::newlines("Testing\n\n\nLine\n\nFeeds", array('limit' => 3)));
        $this->assertEquals("TestingLineFeeds", Sanitize::newlines("Testing\n\nLine\n\nFeeds", array('limit' => 0)));

        $this->assertEquals("Testing\r\nBoth\r\nLineFeeds\r\n\r\nAnd\r\nCarriageReturns", Sanitize::newlines("Testing\r\nBoth\r\r\n\nLineFeeds\r\n\r\r\n\nAnd\r\nCarriageReturns"));
        $this->assertEquals("Testing\r\nBoth\r\nLineFeeds\r\nAnd\r\nCarriageReturns", Sanitize::newlines("Testing\r\nBoth\r\n\r\nLineFeeds\r\n\r\n\r\nAnd\r\nCarriageReturns"));
        $this->assertEquals("Testing\r\nBoth\r\n\r\nLineFeeds\r\n\r\n\r\nAnd\r\nCarriageReturns", Sanitize::newlines("Testing\r\nBoth\r\n\r\nLineFeeds\r\n\r\n\r\nAnd\r\nCarriageReturns", array('crlf' => false)));
    }

    public function testUrl() {
        $this->assertEquals('http://domain.com?key=ber', Sanitize::url('http://domain.com?key=Über'));
        $this->assertEquals('http%3A%2F%2Fdomain.com%3Fkey%3D%C3%9Cber', Sanitize::url(urlencode('http://domain.com?key=Über')));
    }

    public function testWhitespace() {
        $this->assertEquals("Testing White Space", Sanitize::whitespace("Testing  White Space"));
        $this->assertEquals("Testing  White Space", Sanitize::whitespace("Testing  White    Space", array('limit' => 3)));
        $this->assertEquals("TestingWhiteSpace", Sanitize::whitespace("Testing  White    Space", array('limit' => 0)));

        $this->assertEquals("Testing\tTabs", Sanitize::whitespace("Testing\t\t\tTabs", array('tab' => true)));
        $this->assertEquals("Testing\t\tTabs", Sanitize::whitespace("Testing\t\tTabs", array('tab' => true, 'limit' => 3)));
        $this->assertEquals("TestingTabs", Sanitize::whitespace("Testing\tTabs", array('tab' => true, 'limit' => 0)));
    }

    public function testXss() {
        $test = 'Test string <script>alert("XSS!");</script> with attack <div onclick="javascript:alert(\'XSS!\')">vectors</div>';

        // remove HTML tags and escape
        $this->assertEquals('Test string alert(&quot;XSS!&quot;); with attack vectors', Sanitize::xss($test));

        // remove on attributes and escape
        $this->assertEquals('Test string alert(&quot;XSS!&quot;); with attack &lt;div&gt;vectors&lt;/div&gt;', Sanitize::xss($test, array('strip' => false)));

        // remove xmlns and escape
        $this->assertEquals('&lt;html&gt;', Sanitize::xss('<html xmlns="http://www.w3.org/1999/xhtml">', array('strip' => false)));

        // remove namespaced tags and escape
        $this->assertEquals('Content', Sanitize::xss('<ns:tag>Content</ns:tag>', array('strip' => false)));
        $this->assertEquals('Content', Sanitize::xss('<ns:tag attr="foo">Content</ns:tag>', array('strip' => false)));

        // remove unwanted tags
        $this->assertEquals('A string full of unwanted tags.', Sanitize::xss('<audio>A</audio> <script type="text/javascript">string</script> <iframe>full</iframe> <applet>of</applet> <object>unwanted</object> <style>tags</style>.', array('strip' => false)));
    }

}