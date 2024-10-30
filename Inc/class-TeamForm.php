<?php

namespace BetPlugin\Inc;

use BetPlugin\Controllers\Teams as TeamController;

class TeamForm
{

    function __construct()
    {
        add_action('admin_post_save_team', [$this, 'save_team']);
    }

    function save_team()
    {
        if (!wp_verify_nonce($_POST['team_form66'], 'team_form' ) ) wp_die("security issue");
        // wp_die(json_encode($_POST));
        $teamController = new TeamController();
        $data["name"] = sanitize_text_field($_POST["name"]);
        $data["logo"] = esc_url($_POST["logo_input"]);
        if (isset($_POST["id"])) {
            $teamController->update(sanitize_text_field($_POST["id"]), $data);
            wp_safe_redirect(wp_get_referer());
        } else {
            $id = $teamController->create($data);

            $url_destino = add_query_arg(array(
                'id' => $id,
            ), wp_get_referer());

            wp_safe_redirect($url_destino);
        }
    }

    function render()
    {
        if (isset($_GET["id"]) && $_GET["id"] !== '0') {
            $teamController = new TeamController();
            $item =  $teamController->read(sanitize_text_field($_GET["id"]));
            // echo json_encode($item);
        }

        wp_enqueue_media();
?>
        <div>
            <div>
                <a href="<?php echo admin_url('admin.php?page=bet-plugin-em') ?>">
                    << Back to Teams</a>
            </div>
            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field( 'team_form', 'team_form66' ); ?>
                <input type="hidden" name="action" value="save_team">
                <?php
                if (isset($item)) {
                    echo '<input type="hidden" name="id" value="' . sanitize_text_field($item["id"]) . '">';
                }
                ?>
                <h1>Team Form</h1>
                <div>
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?php echo isset($item) ? sanitize_text_field($item["name"]) : '' ?>">
                </div>
                <div>
                    <input type="hidden" name="logo_input" id="logo_input" value="<?php echo isset($item) ? sanitize_text_field($item["logo"]) : '' ?>">
                </div>
                <div>
                    <button type="button" id="logo">Select image</button>
                </div>
                <div id="image-container">
                    <?php
                    if (isset($item)) {
                        echo '<img style="width: 250px;height: 250px;object-fit:cover;" src="' . esc_url($item["logo"]) . '" />';
                    }
                    ?>
                </div>
                <div>
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
<?php
    }
}
