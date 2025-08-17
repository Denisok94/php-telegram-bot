<?php

namespace denisok94\telegram\inline;

/**
 * Summary of BaseResult
 */
class BaseResult implements InlineResultInterface
{
    public function getArray(): array
    {
        $ar = [];
        foreach ($this as $key => $value) {
            $ar[$key] = $value;
        }
        return $ar;
    }
}
