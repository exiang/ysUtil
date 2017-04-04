<?php
 
use Exiang\YsUtil\YsUtil;
 
class YsUtilTest extends PHPUnit_Framework_TestCase {
 
    public function testYsUtilHasCheese()
    {
        $ysUtil = new ysUtil;
        $this->assertTrue($ysUtil->hasCheese());
    }
 
}