<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Venue (место)
 */
class Venue implements InlineResultInterface
{
    public string $id = uniqid();
    public string $type = 'venue';
    public float $latitude;
    public float $longitude;
    public string $title;
    public string $address;
    public string $foursquare_id;
}