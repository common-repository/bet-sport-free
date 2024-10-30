<?php

namespace BetPlugin\Inc;

use BetPlugin\Controllers\Leagues as LeagueController;

class LeaguesForm
{
    function __construct()
    {
        add_action('admin_post_save_league', [$this, 'save_league']);
    }

    function save_league()
    {

        if (!wp_verify_nonce($_POST['league_form66'], 'league_form' ))  wp_die("security issue");

        $leagueController = new LeagueController();
        $data["name"] = sanitize_text_field($_POST["name"]);
        if (isset($_POST["id"])) {
            // update
            $leagueController->update(sanitize_text_field($_POST["id"]), $data);
            wp_safe_redirect(wp_get_referer());
        } else {
            // create
            $id = $leagueController->create($data);
            $url_destino = add_query_arg(array(
                'id' => $id,
            ), wp_get_referer());

            wp_safe_redirect($url_destino);
        }
    }

    function render()
    {
        if (isset($_GET["id"]) && $_GET["id"] !== '0') {
            $leagueController = new LeagueController();
            $item =  $leagueController->read(sanitize_text_field($_GET["id"]));
            // echo json_encode($item);
        }
?>
        <div>
            <a href="<?php echo admin_url('admin.php?page=bet-plugin-leagues') ?>"><< Back to Leagues</a>
            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field( 'league_form', 'league_form66' ); ?>
                <input type="hidden" name="action" value="save_league">
                <?php
                if (isset($item)) {
                    echo '<input type="hidden" name="id" value="' . sanitize_text_field($item["id"]) . '">';
                }
                ?>
                <h1>League Form</h1>
                <div>
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($item) ? sanitize_text_field($item["name"]) : "" ?>">
                </div>
                <div>
                    <button>Save</button>
                </div>
            </form>
        </div>
<?php
    }
}
