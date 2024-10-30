<?php

namespace BetPlugin\Inc;

use BetPlugin\Controllers\Leagues as ControllersLeagues;

class Leagues extends \WP_List_Table
{
    private $per_page = 10;
    private $leagueController;
    function __construct()
    {
        parent::__construct(array(
            'singular' => 'List',
            'plural' => 'Lists',
            'ajax' => false
        ));

        $this->leagueController = new ControllersLeagues;
    }
    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name' => 'Name',
            'shortcode' => 'Shortcode'
        );
        return $columns;
    }
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="lists[]" value="%s" />',
            $item["id"]
        );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
                return $item["id"];
            case 'name':
                return $item["name"];
            case 'shortcode':
                return '[bet-plugin-matches id="' . $item["id"] . '"]';
            default:
                return isset($item["column_name"]) ? $item["column_name"] : '';
        }
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function column_name($item)
    {
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&id=%s">Edit</a>', 'bet-plugin-leagues', $item["id"]),
            'delete'    => sprintf('<a href="?page=%s&action=delete&id=%s">Delete</a>', 'bet-plugin-leagues', $item["id"]),
        );

        return sprintf('%1$s %2$s', $item["name"], $this->row_actions($actions));
    }

    function no_items()
    {
        _e('No Leagues avaliable.');
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items =  $this->leagueController->read();
        //pagination

        $this->process_bulk_action();

        $current_page = $this->get_pagenum();
        $total_items = count($this->items);
        // only ncessary because we have sample data
        $found_data = array_slice($this->items, (($current_page - 1) * $this->per_page), $this->per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $this->per_page                     //WE have to determine how many items to show on a page
        ));
        $this->items = $found_data;
    }

    function process_bulk_action()
    {
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
            $nonce  = sanitize_text_field($_POST['_wpnonce']);
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action))
                wp_die('Nope! Security check failed!');
        }
        $action = $this->current_action();
        switch ($action) {
            case 'delete':
                foreach ($_POST["lists"] as $value) {
                    $this->leagueController->delete($value);
                    // controller to delete
                }
                // $this->render();
                header('Location: ' . esc_url($_SERVER['HTTP_REFERER']));
                // echo '<script>location.reload();</script>';
                // echo json_encode($_POST);
                // wp_redirect(wp_get_referer());
                break;
            default:
                // do nothing or something else
                return;
                break;
        }
        return;
    }
    function render()
    {
?>
        <div class="list-container">
            <form action="" method="post">
                <!-- <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /> -->
                <h2>All Leagues</h2> <a href="<?php echo admin_url('admin.php?page=bet-plugin-leagues&id=0') ?>">Add New League</a>
                <?php
                $this->prepare_items();
                $this->display();
                ?>
            </form>
        </div>
<?php
    }
}
