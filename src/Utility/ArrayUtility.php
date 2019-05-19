<?php
namespace J6s\PhpArch\Utility;


class ArrayUtility
{

    /**
     * Calls the given method for all combinations of 2 elements of the input array
     * (excluding the combination of an element with each self).
     *
     * A call of this method with the input array [ 1, 2, 3 ] will call the given block
     * 6 times with the following arguments: (1, 2) (1, 3) (2, 1) (2, 3) (3, 1) (3,2 ).
     *
     * Scalability warning: From the description of this method it should be obvious that
     * this method scales roughly as O(n^2) - as such it quickly becomes very expensive
     * for large arrays: An element with 50 elements will result in roughly 2500 callback
     * calls.
     *
     * @param array $elements
     * @param callable $block
     */
    public static function forEachCombinationInArray(array $elements, callable $block): void
    {
        foreach ($elements as $first) {
            foreach ($elements as $second) {
                if ($first !== $second) {
                    $block($first, $second);
                }
            }
        }
    }

    /**
     * Calls the given callback for each combination of an element in the first array
     * with each element in the second array.
     *
     * @param array $first
     * @param array $second
     * @param callable $block
     */
    public static function forEachCombination(array $first, array $second, callable $block): void
    {
        foreach ($first as $firstElement) {
            foreach ($second as $secondElement) {
                $block($firstElement, $secondElement);
            }
        }
    }

}
