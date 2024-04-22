<?php declare(strict_types=1);

namespace itnovum\openITCOCKPIT\Perfdata;

use \InvalidArgumentException;

class ScaleType {
    /**
     * 🟦
     * @var string
     */
    public const O = 'O';
    /**
     * 🟨🟩
     * @var string
     */
    public const W_O = 'W<O';
    /**
     * 🟥🟨🟩
     * @var string
     */
    public const C_W_O = 'C<W<O';
    /**
     * 🟩🟨
     * @var string
     */
    public const O_W = 'O<W';
    /**
     * 🟩🟨🟥
     * @var string
     */
    public const O_W_C = 'O<W<C';
    /**
     * 🟥🟨🟩🟨🟥
     * @var string
     */
    public const C_W_O_W_C = 'C<W<O<W<C';
    /**
     * 🟩🟨🟥🟨🟩
     * @var string
     */
    public const O_W_C_W_O = 'O<W<C<W<O';

    /**
     *
     */
    public const ALL = [
        self::O,
        self::W_O,
        self::C_W_O,
        self::O_W,
        self::O_W_C,
        self::C_W_O_W_C,
        self::O_W_C_W_O,
    ];

    /**
     * I will validate if all params are numeric values and if they are in order.
     *
     * If any of the passed arguments is not a number, FALSE will be returned.
     *
     * @return bool
     *
     * @example:
     *      validateOrder(0, 1, 2, 3,    4, 5.6, 6.7, 7.89, 9)  => true  // They are ovisously in order.
     *      validateOrder(0, 1, 2, 2,    2,   2, 6.7, 7.89, 9)  => true  // 2 is repeated, but that's fine
     *      validateOrder(0, 1, 2, 3,    4,   4, 6.7, 7.89, 1)  => false // 1 at the end is not in order.
     *      validateOrder(0, 1, 2, 3, null, 5.6, 6.7, 7.89, 9)  => false // There's a NULL value in here. FALSE
     */
    private static function validateOrder() {
        $last = null;
        $values = func_get_args();
        foreach ($values as $value) {
            if (!is_numeric($value)) {
                return false;
            }

            if ($value >= $last) {
                $last = $value;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * For the given $invert flag and both given Thresholds, I will interprete the correct ScaleType to use.
     *
     * @param bool $invert
     * @param Threshold $warn
     * @param Threshold $crit
     *
     * @return string
     * @throws InvalidArgumentException in case the given parameters don't match into a valid ScaleType.
     *
     */
    public static function get(bool $invert, Threshold $warn, Threshold $crit): string {
        # echo "$crit->low < $warn->low < $warn->high < $crit->high";
        if (false) {
            // Just for legibility
        } else if ($invert && self::validateOrder($crit->low, $warn->low, $warn->high, $crit->high)) {
            return ScaleType::O_W_C_W_O;
        } else if (!$invert && self::validateOrder($crit->low, $warn->low, $warn->high, $crit->high)) {
            return ScaleType::C_W_O_W_C;
        } else if (self::validateOrder($warn->low, $crit->low)) {
            return ScaleType::O_W_C;
        } else if (self::validateOrder($crit->low, $warn->low)) {
            return ScaleType::C_W_O;
        } else if ($warn->high && $crit->high === null) {
            return ScaleType::O_W;
        } else if ($warn->low && $crit->low === null) {
            return ScaleType::W_O;
        } else if ($warn->low === null && $warn->high === null && $crit->low === null && $crit->high === null) {
            return ScaleType::O;
        }

        throw new InvalidArgumentException("This setup is unknown to me. See details.");
    }

    public static function findMin(): ?float {
        $last   = 0;
        $values = func_get_args();
        foreach ($values as $value) {
            if ($value === null) {
                continue;
            }
            if ($value < $last) {
                $last = $value;
            }
        }
        if ($last === null) {
            return null;
        }
        return  (float)$last;
    }

    public static function findMax(): ?float {
        $last = 100;
        $values = func_get_args();
        foreach ($values as $value) {
            if ($value === null) {
                continue;
            }
            if ($value > $last) {
                $last = $value;
            }
        }
        if ($last === null) {
            return null;
        }
        return (float)$last;
    }
}
