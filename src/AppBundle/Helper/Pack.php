<?php

namespace AppBundle\Helper;

class Pack {
    const DEFAULT_NUMBER_OF_PLAYERS = 3;
    const GIGA_NUMBER_OF_PLAYERS = 9;

    const SILVER_PACK_PRICE = 300;
    const UNIQ_SILVER_PLAYER = 100;

    const GOLDEN_PACK_PRICE = 1500;
    const UNIQ_GOLDEN_PLAYER = 500;

    const GIGA_PACK_PRICE = 4500;
    const UNIQ_GIGA_PLAYER = 500;

    const SUPER_RARE_PACK_PRICE = 10500;
    const UNIQ_SUPER_RARE_PLAYER = 3500;

    const ULTRA_RARE_PACK_PRICE = 20000;

    const SILVER_PACK_LABEL = 'silver';
    const GOLDEN_PACK_LABEL = 'golden';
    const SUPER_RARE_PACK_LABEL = 'super-rare';
    const GIGA_PACK_LABEL = 'giga';

    const PACK_LABEL = 'pack';
    const PACK_DISABLE_LABEL = 'pack-disable';

    const MINIMUM_RARE_RATING = 85;
    const MAXIMUM_RARE_RATING = 87;
    const MINIMUM_ULTRA_RARE_RATING = 90;
    const MAXIMUM_ULTRA_RARE_RATING = 94;
    const EPIC_RATING = 95;

    const UNIQ_REFUND_SILVER_PLAYER = 100;
    const UNIQ_REFUND_GOLDEN_PLAYER = 500;
    const UNIQ_REFUND_SUPER_RARE_PLAYER = 1000;
    const UNIQ_REFUND_EPIC_PLAYER = 2000;
    const UNIQ_REFUND_ULTRA_RARE_PLAYER = 5000;

    private $packs;

    public function __construct()
    {
        $this->packs = [];

        $this->packs['silver'] = ["name" => "SILVER PACK"];
        $this->packs['golden'] = ["name" =>"GOLDEN PACK"];
        $this->packs['giga'] = ["name" =>"GIGA PACK"];
        $this->packs['super-rare'] = ["name" =>"SUPER RARE PACK"];
    }

    /**
     * Create Pack List
     *
     * @return array
     */
    public function getPackList($user) {

        $silverPack = Pack::PACK_LABEL;
        $goldenPack = Pack::PACK_LABEL;
        $gigaPack = Pack::PACK_LABEL;
        $superRarePack = Pack::PACK_LABEL;

        $packs = $this->disablePack($user, $silverPack, $goldenPack, $gigaPack, $superRarePack);

        $packs["silver"]["price"] = Pack::SILVER_PACK_PRICE;
        $packs["golden"]["price"] = Pack::GOLDEN_PACK_PRICE;
        $packs["giga"]["price"] = Pack::GIGA_PACK_PRICE;
        $packs["super-rare"]["price"] = Pack::SUPER_RARE_PACK_PRICE;

        return $packs;
    }

    /**
     * Disable pack if not enough points
     *
     * @param $user
     * @param $silverPack
     * @param $goldenPack
     * @param $gigaPack
     * @param $superRarePack
     * @return array
     */
    public function disablePack($user, $silverPack, $goldenPack, $gigaPack, $superRarePack) {
        if ($user->getQuizPoints() < Pack::SILVER_PACK_PRICE) { $silverPack = Pack::PACK_DISABLE_LABEL; };
        $this->packs['silver']['class'] = $silverPack;

        if ($user->getQuizPoints() < Pack::GOLDEN_PACK_PRICE) { $goldenPack = Pack::PACK_DISABLE_LABEL; };
        $this->packs['golden']['class'] = $goldenPack;

        if ($user->getQuizPoints() < Pack::GIGA_PACK_PRICE) { $gigaPack = Pack::PACK_DISABLE_LABEL; };
        $this->packs['giga']['class'] = $gigaPack;

        if ($user->getQuizPoints() < Pack::SUPER_RARE_PACK_PRICE) { $superRarePack = Pack::PACK_DISABLE_LABEL; };
        $this->packs['super-rare']['class'] = $superRarePack;

        return $this->packs;
    }
}