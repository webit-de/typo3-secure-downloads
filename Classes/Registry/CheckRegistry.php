<?php
declare(strict_types = 1);
namespace Leuchtfeuer\SecureDownloads\Registry;

/***
 *
 * This file is part of the "Secure Downloads" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 *
 ***/

use Leuchtfeuer\SecureDownloads\Exception\ClassNotFoundException;
use Leuchtfeuer\SecureDownloads\Exception\InvalidClassException;
use Leuchtfeuer\SecureDownloads\Security\AbstractCheck;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CheckRegistry extends AbstractRegistry
{
    protected static $checks = [];

    public static function register(string $identifier, string $className, int $priority = 0, bool $overwriteExisting = false): void
    {
        if (isset(self::$checks[$identifier]) && $overwriteExisting === false) {
            // Do nothing. Maybe log this in future.
            return;
        }

        self::$checks[$identifier] = [
            'class' => self::getCheckFromClassName($className),
            'priority' => $priority
        ];

        self::sortByPriority(self::$checks);
    }

    public static function getChecks(): array
    {
        return self::$checks;
    }

    private static function getCheckFromClassName(string $className): AbstractCheck
    {
        if (!class_exists($className)) {
            throw new ClassNotFoundException(
                sprintf('Class "%s" not found.', $className),
                1588837466
            );
        }

        $check = GeneralUtility::makeInstance($className);

        if (!$check instanceof AbstractCheck) {
            throw new InvalidClassException(
                sprintf('Class "%s" must extend "%s"', $className, AbstractCheck::class),
                1588837696
            );
        }

        return $check;
    }
}
