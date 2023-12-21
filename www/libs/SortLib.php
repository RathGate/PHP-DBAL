<?php

namespace libs;

class SortLib {
    static function InsertionSort($arr){
        for ($i = 0; $i < count($arr); $i++) {
            $currentValue = $arr[$i];
            $prevIndex = $i - 1;
            while ($prevIndex >= 0 && $currentValue < $arr[$prevIndex]) {
                $arr[$prevIndex + 1] = $arr[$prevIndex];
                $prevIndex = $prevIndex - 1;
            }
            $arr[$prevIndex+1] = $currentValue;
        }
        return $arr;
    }

    static function BubbleSort($arr) {
        for ($i = 0; $i < count($arr); $i++) {
            $hasSwapped = false;
            for ($j = 0; $j < count($arr) - $i - 1; $j++) {
                if ($arr[$j] > $arr[$j+1]) {
                    $temp = $arr[$j];
                    $arr[$j] = $arr[$j+1];
                    $arr[$j+1] = $temp;
                    $hasSwapped = true;
                }
            }
            if (!$hasSwapped) {
                break;
            }
        }
        return $arr;
    }
    static function QuickSort($arr)
    {
        if(count($arr) <= 1){
            return $arr;
        }
        else{
            $pivot = $arr[0];
            $left = array();
            $right = array();
            for($i = 1; $i < count($arr); $i++)
            {
                if($arr[$i] < $pivot){
                    $left[] = $arr[$i];
                }
                else{
                    $right[] = $arr[$i];
                }
            }
            return array_merge(self::QuickSort($left), array($pivot), self::QuickSort($right));
        }
    }
}