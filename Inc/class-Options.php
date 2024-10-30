<?php

namespace BetPlugin\Inc;

use BetPlugin\Controllers\Bet as BetController;
use BetPlugin\Controllers\Leagues as LeagueController;
use BetPlugin\Controllers\Matches as MatchesController;
use BetPlugin\Controllers\Teams as TeamsController;

class Options
{
    private $leaguesController;
    private $teamController;
    private $matchesController;
    private $betController;
    private $teamForm;
    private $leagueForm;
    private $matchesForm;
    function __construct()
    {
        $this->leaguesController = new LeagueController;
        $this->teamController = new TeamsController;
        $this->matchesController = new MatchesController;
        $this->betController = new BetController;
        $this->teamForm = new TeamForm;
        $this->leagueForm = new LeaguesForm;
        $this->matchesForm = new MatchesForm;
        add_action('admin_menu', [$this, 'register_menu']);
    }

    function register_menu()
    {
        add_menu_page('Bet Plugin ', 'Bet Plugin', 'manage_options', 'bet-plugin-em', [$this, 'teams'], BETPLUGIN_PLUGIN_URL . '/images/logo_menu.png', null);
        add_submenu_page('bet-plugin-em', 'Bet Plugin Teams', 'Teams', 'manage_options', 'bet-plugin-em', [$this, 'teams'], null);
        add_submenu_page('bet-plugin-em', 'Bet Plugin Leagues', 'Leagues', 'manage_options', 'bet-plugin-leagues', [$this, 'leagues'], null);
        add_submenu_page('bet-plugin-em', 'Bet Plugin Matches', 'Matches', 'manage_options', 'bet-plugin-matches', [$this, 'matches'], null);
        add_submenu_page('bet-plugin-em', 'Bet Plugin Bets', 'Bets', 'manage_options', 'bet-plugin-bets', [$this, 'bets'], null);
        add_submenu_page('bet-plugin-em', 'Bet Plugin Settings', 'Settings', 'manage_options', 'bet-plugin-settings', [$this, 'settings'], null);
    }

    function settings()
    {
?>
        <div>
            <h1>Bet Plugin Settings</h1>
            <hr>
            <h2>Instructions</h2>
            <div>
                You can insert matches shortcode with <i>League id </i> or Empty
            </div>
            <em>[bet-plugin-matches id="51079"]</em>
        </div>
<?php
    }

    function bets()
    {
        if (isset($_GET["action"]) && $_GET["action"] == "delete") {
            $this->betController->delete(sanitize_text_field($_GET["id"]));
            header('Location: ' . esc_url($_SERVER['HTTP_REFERER']));
        } else {
            $l = new Bets;
            $l->render();
        }
    }

    function leagues()
    {

        if (isset($_GET["action"]) && $_GET["action"] == "delete") {
            $this->leaguesController->delete(sanitize_text_field($_GET["id"]));
            header('Location: ' . esc_url($_SERVER['HTTP_REFERER']));
        } else if (isset($_GET["id"]) && !isset($_GET["action"])) {
            $this->leagueForm->render();
        } else {
            $l = new Leagues();
            $l->render();
        }
    }

    function matches()
    {
        if (isset($_GET["action"]) && $_GET["action"] == "delete") {
            $this->matchesController->delete(sanitize_text_field($_GET["id"]));
            header('Location: ' . esc_url($_SERVER['HTTP_REFERER']));
        } else if (isset($_GET["id"]) && !isset($_GET["action"])) {
            $this->matchesForm->render();
        } else {
            $l = new Matches();
            $l->render();
        }
    }

    function teams()
    {
        if (isset($_GET["action"]) && $_GET["action"] === "delete") {
            $this->teamController->delete(sanitize_text_field($_GET["id"]));
            header('Location: ' . esc_url($_SERVER['HTTP_REFERER']));
        } else if (isset($_GET["id"]) && !isset($_GET["action"])) {
            $this->teamForm->render();
        } else {
            $l = new Teams();
            $l->render();
        }
    }
}
