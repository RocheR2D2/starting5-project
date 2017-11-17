<?php
namespace AppBundle\Twig;

class PlayerExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('teamName', array($this, 'getTeamName')),
        );
    }

    public function getTeamName($teamId){
        return $teamId;
    }
}