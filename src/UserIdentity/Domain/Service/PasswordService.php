<?php

namespace UserIdentity\Domain\Model\Service;

use Webmozart\Assert\Assert;

class PasswordService
{
    const DIGITS                = "0123456789";
    const LETTERS               = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const STRONG_THRESHOLD      = 20;
    const SYMBOLS               = "\"`!?$?%^&*()_-+={[}]:;@'~#|\\<,>.?/";
    const VERY_STRONG_THRESHOLD = 40;

    /**
     * @return string
     */
    public function generateStrongPassword()
    {
        $generatedPassword = null;
        $password = '';

        $isStrong = false;

        while (!$isStrong) {
            $opt = rand(0, 4);

            switch ($opt) {
                case 0:
                    $index = rand(0, mb_strlen(self::LETTERS));
                    $password .= mb_substr(self::LETTERS, $index, $index + 1);
                    break;
                case 1:
                    $index = rand(0, mb_strlen(self::LETTERS));
                    $password .= mb_strtolower(substr(self::LETTERS, $index, $index + 1));
                    break;
                case 2:
                    $index = rand(0, mb_strlen(self::DIGITS));
                    $password .= mb_substr(self::DIGITS, $index, $index + 1);
                    break;
                case 3:
                    $index = rand(0, mb_strlen(self::SYMBOLS));
                    $password .= mb_substr(self::SYMBOLS, $index, $index + 1);
                    break;
            }

            $generatedPassword = (string)$password;

            if (strlen($generatedPassword) >= 7) {
                $isStrong = $this->isStrong($generatedPassword);
            }
        }

        return $password;
    }

    /**
     * @param $aPlainTextPassword
     * @return bool
     */
    public function isStrong($aPlainTextPassword)
    {
        return $this->calculatePasswordStrength($aPlainTextPassword) >= self::STRONG_THRESHOLD;
    }

    /**
     * @param $aPlainTextPassword
     * @return bool
     */
    public function isVeryStrong($aPlainTextPassword)
    {
        return $this->calculatePasswordStrength($aPlainTextPassword) >= self::VERY_STRONG_THRESHOLD;
    }

    /**
     * @param $aPlainTextPassword
     * @return bool
     */
    public function isWeak($aPlainTextPassword)
    {
        return $this->calculatePasswordStrength($aPlainTextPassword) < self::STRONG_THRESHOLD;
    }

    /**
     * @param $aPlainTextPassword
     * @return int
     */
    private function calculatePasswordStrength($aPlainTextPassword)
    {
        Assert::notNull($aPlainTextPassword, 'Password strength cannot be tested on null.');

        $strength = 0;

        $length = mb_strlen($aPlainTextPassword);

        if ($length > 7) {
            $strength += 10;
            // bonus: one point each additional
            $strength += ($length - 7);
        }

        $digitCount = 0;
        $letterCount = 0;
        $lowerCount = 0;
        $upperCount = 0;
        $symbolCount = 0;

        $splitPassword = str_split($aPlainTextPassword);

        for ($idx = 0; $idx < $length; ++$idx) {
            $ch = $splitPassword[0];

            if (ctype_alpha($ch)) {
                ++$letterCount;
                if (ctype_upper($ch)) {
                    ++$upperCount;
                } else {
                    ++$lowerCount;
                }
            } elseif (ctype_digit($ch)) {
                ++$digitCount;
            } else {
                ++$symbolCount;
            }
        }

        $strength += ($upperCount + $lowerCount + $symbolCount);

        // bonus: letters and digits
        if ($letterCount >= 2 && $digitCount >= 2) {
            $strength += ($letterCount + $digitCount);
        }

        return $strength;
    }
}