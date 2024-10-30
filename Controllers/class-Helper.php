<?php

namespace BetPlugin\Controllers;

use BetPlugin\Controllers\Matches as MatcheController;

class Helper
{
    function get_matches()
    {
        $matchesController = new MatcheController;
        return $matchesController->read();
    }
}
