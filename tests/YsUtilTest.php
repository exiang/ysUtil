<?php
 
use Exiang\YsUtil\YsUtil;
 
class YsUtilTest extends PHPUnit_Framework_TestCase {
 
    /*public function testYsSendMail()
    {
        $ysUtil = new ysUtil;
        $receivers[] = array('email'=>'exiang83@gmail.com', 'name'=>'Allen Tan');
        $subject = 'Test email from YsUtilTest';
        $message = "This is the body message";
        $smtpParams['smtpHost'] = 'smtp.mandrillapp.com';
		$smtpParams['smtpSecure']  = 'tls';
		$smtpParams['smtpPort'] = '587'; 
		$smtpParams['smtpAuth'] = true;
        $smtpParams['smtpUsername'] = 'cloud@mymagic.my'; 
        // note: please insert password here and remove before commit to repo!
		$smtpParams['smtpPassword'] = ''; 
		$smtpParams['smtpSenderEmail'] = 'noreply@mymagic.my'; 
		$smtpParams['smtpSenderName'] = 'Tan Yee Siang'; 
		$smtpParams['emailPrefix'] = 'YsUtilTest'; 
		$smtpParams['blockSendMail'] = false; 
        $smtpParams['logSentMail'] = false;
        
        $this->assertTrue($ysUtil->sendMail($receivers, $subject, $message, $smtpParams));
    }*/

    public function testYsUtilHasCheese()
    {
        $ysUtil = new ysUtil;
        $this->assertTrue($ysUtil->hasCheese());
    }

    public function testYsUtilHtml2Text()
    {
        $ysUtil = new ysUtil;
        $this->assertSame($ysUtil->html2text('Hello, &quot;<b>world</b>&quot;'), 'Hello, "WORLD"');
    }
    
    public function testYsUtilTimezone2Offset()
    {
        $ysUtil = new ysUtil;
        $this->assertSame($ysUtil->timezone2offset('Asia/Kuala_Lumpur'), '+08:00');
    }
    
    public function testYsUtilIsEmailAddress()
    {
        $ysUtil = new ysUtil;
        $this->assertTrue($ysUtil->isEmailAddress('email@gmail.com'));
        $this->assertTrue($ysUtil->isEmailAddress('email@domain-with-dash.com'));
        $this->assertTrue($ysUtil->isEmailAddress('emailCamel@gmail.com'));
        $this->assertTrue($ysUtil->isEmailAddress('email.dot@gmail.com'));
        $this->assertTrue($ysUtil->isEmailAddress('email+plus@gmail.com'));
        $this->assertFalse($ysUtil->isEmailAddress('email.dot@gmail+123.com'));
    }
 
}