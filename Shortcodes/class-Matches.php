<?php

namespace BetPlugin\Shortcodes;

use BetPlugin\Controllers\Bet as BetController;
use BetPlugin\Controllers\Helper;
use BetPlugin\Controllers\Leagues as LeagueController;
use BetPlugin\Controllers\Matches as MatchesControllers;

class Matches
{

    function __construct()
    {
        add_shortcode('bet-plugin-matches', [$this, 'render']);
        add_action('wp_ajax_getdata', [$this, 'getdata_callback']);
        add_action('wp_ajax_nopriv_getdata', [$this, 'getdata_callback']);

        add_action('wp_ajax_savebet', [$this, 'savebet_callback']);
        add_action('wp_ajax_nopriv_savebet', [$this, 'savebet_callback']);
    }

    function savebet_callback()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'bet')) wp_die("security issue");

        $match_id = sanitize_text_field($_POST["id"]);

        $errors = $this->validate($_POST);

        if (!empty($errors)) wp_send_json_error($errors);

        $matchController = new MatchesControllers;
        $match = $matchController->read($match_id);

        $data["match"] = $match["team1"]["name"] . ' - ' . $match["team2"]["name"];
        $data["score"] = sanitize_text_field($_POST["score"]);
        $data["date"] = date('Y-m-d H:i:s');
        $data["user"] = get_current_user();

        $betController = new BetController;
        $betController->create($data);
        wp_send_json_success(["Saved bet"]);
    }

    function validate($data)
    {
        $errors = [];
        if (!is_user_logged_in()) {
            $errors[] = "You must be logged in to bet ";
        }
        $scores = explode('-', $data["score"]);
        if ($scores[0] > 10 || $scores[1] > 10) {
            $errors[] = "You cant bet over 10 to each team";
        }
        if ($scores[0] === "" || $scores[1] === "") {
            $errors[] = "Your bet cant be empty";
        }
        if (!ctype_digit($scores[0]) || !ctype_digit($scores[1])) {
            $errors[] = "Your bet is not valid";
        }
        return $errors;
    }

    function getdata_callback()
    {
        $league = sanitize_text_field($_POST["league"]);

        $helper = new Helper();
        $data = $helper->get_matches();

        if (is_numeric($league)) {
            $data = array_filter($data, function ($d) use ($league) {
                return $d["league"]["id"] == $league;
            });
        } else if ($league === "none") {
            $data = [];
        }

        wp_send_json($data);
        wp_die();
    }

    function render($atts)
    {
        $atts = shortcode_atts([
            'id' => null
        ], $atts);

        $leagueController = new LeagueController;
        $leagues = $leagueController->read();

        $leagues_ids = array_column($leagues, 'id');
        if ($atts["id"] == null) $atts["id"] = "all";
        else if (!in_array($atts["id"], $leagues_ids)) $atts["id"] = "none";

        ob_start();
?>
        <div id="app" data-league="<?php echo sanitize_text_field($atts["id"]) ?>"></div>

<?php
        return ob_get_clean();
    }
}
