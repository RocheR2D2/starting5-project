<?php

namespace AppBundle\Helper;

class Player {

    public $mapping;

    public function __construct() {
        $mapping = [
            1 => Pack::UNIQ_REFUND_ULTRA_RARE_PLAYER,
            2 => Pack::UNIQ_REFUND_EPIC_PLAYER,
            3 => Pack::UNIQ_REFUND_SUPER_RARE_PLAYER,
            4 => Pack::UNIQ_SILVER_PLAYER,
            5 => Pack::UNIQ_SILVER_PLAYER,
            6 => Pack::UNIQ_SILVER_PLAYER,
            7 => Pack::UNIQ_SILVER_PLAYER,
        ];

        $this->mapping = $mapping;
    }
}