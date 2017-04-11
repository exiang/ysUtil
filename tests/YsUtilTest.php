<?php
 
use Exiang\YsUtil\YsUtil;
 
class YsUtilTest extends PHPUnit_Framework_TestCase {
 
    public function testYsSendMail()
    {
        $ysUtil = new ysUtil;
        $receivers[] = array('email'=>'exiang83@gmail.com', 'name'=>'Allen Tan');
        $subject = 'Test email from YsUtilTest';
        $message = "This is the body message";
        $smtpParams['smtpHost'] = 'email-smtp.us-east-1.amazonaws.com';
		$smtpParams['smtpSecure']  = 'tls';
		$smtpParams['smtpPort'] = '587'; 
		$smtpParams['smtpAuth'] = true;
		$smtpParams['smtpUsername'] = 'AKIAJPZTWSLKXCGWQBOA'; 
		$smtpParams['smtpPassword'] = 'AvJX5bpsot1AsfgKW/TwAntMhPReN8cka67f2sNXR0tb'; 
		$smtpParams['smtpSenderEmail'] = 'exiang83@yahoo.com'; 
		$smtpParams['smtpSenderName'] = 'Tan Yee Siang'; 
		$smtpParams['emailPrefix'] = 'YsUtilTest'; 
		$smtpParams['blockSendMail'] = false; 
		$smtpParams['logSentMail'] = false;
        $this->assertTrue($ysUtil->sendMail($receivers, $subject, $message, $smtpParams));
    }

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