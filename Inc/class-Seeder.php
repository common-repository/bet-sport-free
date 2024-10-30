<?php

namespace BetPlugin\Inc;

use BetPlugin\Controllers\Leagues as LeagueController;
use BetPlugin\Controllers\Matches as MatchesController;
use BetPlugin\Controllers\Teams as TeamsController;

class Seeder
{
    private $leagueController;
    private $teamsController;
    private $matchesController;

    function __construct()
    {
        $this->leagueController = new LeagueController;
        $this->teamsController = new TeamsController;
        $this->matchesController = new MatchesController;
        $this->seed();
    }

    function seed()
    {

        if (get_option('em_bet_seed')) return;

        $data_teams = $this->teams();

        $teams_ids = [];
        foreach ($data_teams as $t) {
            $teams_ids[] = $this->teamsController->create($t);
        }
        $league["name"] = "Champions";
        $league_id = $this->leagueController->create($league);

        $matches = $this->matches($teams_ids, $league_id);

        foreach ($matches as $m) {
            $this->matchesController->create($m);
        }

        update_option('em_bet_seed', true);
    }

    function teams()
    {
        return [
            [
                "name" => "Manchester City",
                "logo" => "https://1.bp.blogspot.com/-YTjZGcNmUos/WOjCBuRmMUI/AAAAAAAAL-8/nEK3LQiceIQSQu1CIY5T3E6z6eH7qfOlgCLcB/s1600/01_fc-manchester-united-logo.png"
            ],
            [
                "name" => "Chelsea",
                "logo" => "https://w7.pngwing.com/pngs/489/524/png-transparent-chelsea-f-c-football-team-1970-fa-cup-final-uefa-champions-league-football-blue-emblem-sport.png"
            ],
            [
                "name" => "Barcelona",
                "logo" => "https://seeklogo.com/images/F/FC_Barcelona-logo-CE8D51F664-seeklogo.com.png"
            ],
            [
                "name" => "Bayer",
                "logo" => "https://upload.wikimedia.org/wikipedia/commons/0/0c/FC_Bayern_M%C3%BCnchen_Logo_2017.png"
            ]
        ];
    }

    function matches($teams_ids, $league_id)
    {
        return [
            [
                "league_id" => $league_id,
                "team1_id" => $teams_ids[0],
                "team2_id" => $teams_ids[1],
                "date" => date('Y-m-d\TH:i:sP', strtotime('+10 days')),
                "stage" => "Group A",
                "stadium" => "Falmer Stadium",
                "score" => ""
            ],
            [
                "league_id" => $league_id,
                "team1_id" => $teams_ids[2],
                "team2_id" => $teams_ids[3],
                "date" => date('Y-m-d\TH:i:sP', strtotime('+10 days')),
                "stage" => "Group A",
                "stadium" => "City Ground",
                "score" => ""
            ]
        ];
    }
}
