<?php

namespace Fnugg\Data;
defined('ABSPATH') || die;

interface Data
{
    public function autocomplete(array $q) : array;

    public function search(array $q) : array;
}
