<?php

namespace tests\UserIdentity\Domain\Model;

use UserIdentity\Domain\Model\Service\PasswordService;

class PasswordServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function generateStrongPassword()
    {
        $password = $this->getPasswordService()->generateStrongPassword();

        $this->assertTrue($this->getPasswordService()->isStrong($password));
        $this->assertFalse($this->getPasswordService()->isWeak($password));
    }

    /**
     * @test
     */
    public function isStrongPassword()
    {
        $password = 'Th1sShudBStrong';

        $this->assertTrue($this->getPasswordService()->isStrong($password));
        $this->assertFalse($this->getPasswordService()->isVeryStrong($password));
        $this->assertFalse($this->getPasswordService()->isWeak($password));
    }

    /**
     * @test
     */
    public function isVeryStrongPassword()
    {
        $password = 'Th1sSh0uldBV3ryStrong';

        $this->assertTrue($this->getPasswordService()->isStrong($password));
        $this->assertTrue($this->getPasswordService()->isVeryStrong($password));
        $this->assertFalse($this->getPasswordService()->isWeak($password));
    }

    /**
     * @test
     */
    public function isWeakPassword()
    {
        $password = 'Weakness';

        $this->assertFalse($this->getPasswordService()->isStrong($password));
        $this->assertFalse($this->getPasswordService()->isVeryStrong($password));
        $this->assertTrue($this->getPasswordService()->isWeak($password));
    }

    private function getPasswordService()
    {
        return new PasswordService();
    }
}