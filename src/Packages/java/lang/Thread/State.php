<?php
declare(strict_types=1);
namespace PHPJava\Packages\java\lang\Thread;

use PHPJava\Exceptions\NotImplementedException;
use PHPJava\Packages\java\lang\Enum;

// use PHPJava\Packages\java\io\Serializable;
// use PHPJava\Packages\java\lang\Comparable;

/**
 * The `State` class was auto generated.
 *
 * @parent \PHPJava\Packages\java\lang\Object_
 * @parent \PHPJava\Packages\java\lang\Enum
 */
class State extends Enum // implements Serializable, Comparable
{
    // Thread state for a thread blocked waiting for a monitor lock.
    const BLOCKED = 'BLOCKED';

    // Thread state for a thread which has not yet started.
    const _NEW = '_NEW';

    // Thread state for a runnable thread.
    const RUNNABLE = 'RUNNABLE';

    // Thread state for a terminated thread.
    const TERMINATED = 'TERMINATED';

    // Thread state for a waiting thread with a specified waiting time.
    const TIMED_WAITING = 'TIMED_WAITING';

    // Thread state for a waiting thread.
    const WAITING = 'WAITING';

    /**
     * Returns the enum constant of this type with the specified name.
     *
     * @see https://docs.oracle.com/en/java/javase/11/docs/api/java.base/java/lang/package-summary.html#valueOf
     * @param null|mixed $a
     * @throws NotImplementedException
     */
    public static function static_valueOf($a = null)
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Returns an array containing the constants of this enum type, inthe order they are declared.
     *
     * @see https://docs.oracle.com/en/java/javase/11/docs/api/java.base/java/lang/package-summary.html#values
     * @param null|mixed $a
     * @throws NotImplementedException
     */
    public static function static_values($a = null)
    {
        throw new NotImplementedException(__METHOD__);
    }
}
