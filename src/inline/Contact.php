<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Contact (контакт)
 */
class Contact extends BaseResult
{
    public string $id;
    public string $type = 'contact';
    public string $phone_number;
    public string $first_name;
    public string $last_name;
    /**
     * BEGIN:VCARD\nVERSION:3.0\nFN:Иван Иванов\nTEL;TYPE=mobile:+79991234567\nEND:VCARD
     */
    public string $vcard;
}