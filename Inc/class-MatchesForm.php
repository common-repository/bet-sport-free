<?php

namespace BetPlugin\Inc;

use BetPlugin\Controllers\Leagues as LeagueController;
use BetPlugin\Controllers\Matches as MatchesController;
use BetPlugin\Controllers\Teams as TeamController;

class MatchesForm
{
    private $matchController;
    function __construct()
    {
        $this->matchController = new MatchesController;
        add_action('admin_post_save_match', [$this, 'save_match']);
    }

    function save_match()
    {

        if (!wp_verify_nonce($_POST['matches_form66'], 'matches_form' ) ) wp_die("security issue");

        if (isset($_POST["id"])) {
            $this->matchController->update(sanitize_text_field($_POST["id"]), $_POST);
            wp_safe_redirect(wp_get_referer());
        } else {
            $id = $this->matchController->create($_POST);
            $url_destino = add_query_arg(array(
                'id' => $id,
            ), wp_get_referer());

            wp_safe_redirect($url_destino);
        }

        // wp_die(json_encode($_POST));
    }

    function render()
    {
        $leagueController = new LeagueController;
        $leagues = $leagueController->read();
        // echo json_encode($leagues);
        $teamController = new TeamController;
        $teams = $teamController->read();
        // echo json_encode($teams);

        if (isset($_GET["id"]) && $_GET["id"] !== "0") {
            $item = $this->matchController->read(sanitize_text_field($_GET["id"]));
        }
?>
        <div>
            <div>
                <a href="<?php echo admin_url('admin.php?page=bet-plugin-matches') ?>">
                    << Back to Matches</a>
            </div>
            <h1>Matches Form</h1>
            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field( 'matches_form', 'matches_form66' ); ?>
                <input type="hidden" name="action" value="save_match">
                <?php if (isset($item)) echo '<input type="hidden" name="id" value="' . sanitize_text_field($item["id"]) . '">'; ?>
                <div>
                    <label for="league">League</label>
                    <select name="league_id" id="league">
                        <?php foreach ($leagues as $l) : ?>
                            <option <?php echo isset($item) && $item["league"]["id"] == $l["id"] ? "selected" : "" ?> value="<?php echo sanitize_text_field($l["id"]) ?>"><?php echo sanitize_text_field($l["name"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="team1">Team1</label>
                    <select name="team1_id" id="team1">
                        <?php foreach ($teams as $t) : ?>
                            <option <?php echo isset($item) && $item["team1"]["id"] == $t["id"] ? "selected" : "" ?> value="<?php echo sanitize_text_field($t["id"]) ?>"><?php echo sanitize_text_field($t["name"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="team2">Team2</label>
                    <select name="team2_id" id="team2">
                        <?php foreach ($teams as $t) : ?>
                            <option <?php echo isset($item) && $item["team2"]["id"] == $t["id"] ? "selected" : "" ?> value="<?php echo sanitize_text_field($t["id"]) ?>"><?php echo sanitize_text_field($t["name"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="date">Date</label>
                    <input type="datetime-local" name="date" id="date" value="<?php echo isset($item) ? sanitize_text_field($item["date"]) : "" ?>">
                </div>
                <div>
                    <label for="stage">Stage</label>
                    <input type="text" name="stage" id="stage" value="<?php echo isset($item) ? sanitize_text_field($item["stage"]) : "" ?>">
                </div>
                <div>
                    <label for="stadium">Stadium</label>
                    <input type="text" name="stadium" id="stadium" value="<?php echo isset($item) ? sanitize_text_field($item["stadium"]) : "" ?>">
                </div>
                <div>
                    <label for="score">Score</label>
                    <input type="text" pattern="\d+-\d+" title="Por favor ingrese el formato número-número" name="score" id="score" value="<?php echo isset($item) ? sanitize_text_field($item["score"]) : "" ?>">
                    <small>example: 1-1</small>
                </div>
                <div>
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
<?php
    }
}
