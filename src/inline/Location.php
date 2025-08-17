<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Location (местоположение)
 */
class Location extends BaseResult
{
    public string $id;
    public string $type = 'location';
    public float $latitude;
    public float $longitude;
    public string $title;
}