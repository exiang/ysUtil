<?php
 
use Exiang\YsUtil\YsUtil;
 
class YsUtilTest extends PHPUnit_Framework_TestCase {
 
    public function testYsUtilHasCheese()
    {
        $ysUtil = new ysUtil;
        $this->assertTrue($ysUtil->hasCheese());
    }

    public function testYsUtilHtml2Text()
    {
        $ysUtil = new ysUtil;
        $this->assertEquals($ysUtil->html2text('Hello, &quot;<b>world</b>&quot;'), 'Hello, "WORLD"');
    }
    
    public function testYsUtilTimezone2Offset()
    {
        $ysUtil = new ysUtil;
        $this->assertEquals($ysUtil->timezone2offset('Asia/Kuala_Lumpur'), '+08:00');
    }
 
}