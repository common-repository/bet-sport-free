<?php

namespace BetPlugin\Inc;

use BetPlugin\Controllers\Bet as BetController;

class Bets extends \WP_List_Table
{
    private $per_page = 10;
    private $betController;
    function __construct()
    {
        parent::__construct(array(
            'singular' => 'List',
            'plural' => 'Lists',
            'ajax' => false
        ));

        $this->betController = new BetController;
    }
    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'date' => 'Date',
            'match' => 'Match',
            'score' => 'Score',
            'user' => 'User'
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
            case 'date':
                return $item["date"];
            case 'match':
                return $item["match"];
            case 'score':
                return $item["score"];
            case 'user':
                return $item["user"];
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

    function column_date($item)
    {
        $actions = array(
            // 'edit'      => sprintf('<a href="?page=%s&id=%s">Edit</a>', 'bet-plugin-bets', $item["id"]),
            'delete'    => sprintf('<a href="?page=%s&action=delete&id=%s">Delete</a>', 'bet-plugin-bets', $item["id"]),
        );

        return sprintf('%1$s %2$s', $item["date"], $this->row_actions($actions));
    }

    function no_items()
    {
        _e('No Bets avaliable.');
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items =  $this->betController->read();
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
                    $this->betController->delete($value);
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
                <h2>All Users Bets</h2>
                <?php
                $this->prepare_items();
                $this->display();
                ?>
            </form>
        </div>
<?php
    }
}
