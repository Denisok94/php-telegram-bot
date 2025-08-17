<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Location (местоположение)
 */
class Location implements InlineResultInterface
{
    public string $id = uniqid();
    public string $type = 'location';
    public float $latitude;
    public float $longitude;
    public string $title;
}