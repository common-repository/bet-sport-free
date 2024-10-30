<?php

namespace BetPlugin\Inc;

class BetPlugin
{
    function __construct()
    {

        add_action('init', [$this, 'create_post_type']);

        add_action('wp_enqueue_scripts', [$this, 'public_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_script']);
    }

    function admin_script()
    {
        wp_enqueue_style('betplugin-admin', BETPLUGIN_PLUGIN_URL . '/dist/admin.css', [], BETPLUGIN_VERSION, 'all');
        wp_enqueue_script('betplugin-admin', BETPLUGIN_PLUGIN_URL . '/dist/admin.js', [], BETPLUGIN_VERSION, true);
        wp_enqueue_script('betplugin-public', BETPLUGIN_PLUGIN_URL . '/dist/public.js', [], BETPLUGIN_VERSION, true);
        wp_localize_script('betplugin-public', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'f_nonce' => wp_create_nonce('bet')));
    }

    function public_scripts()
    {
        wp_enqueue_style('betplugin-public', BETPLUGIN_PLUGIN_URL . '/dist/public.css', [], BETPLUGIN_VERSION, 'all');
        wp_enqueue_script('betplugin-public', BETPLUGIN_PLUGIN_URL . '/dist/public.js', [], BETPLUGIN_VERSION, true);
        wp_localize_script('betplugin-public', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'f_nonce' => wp_create_nonce('bet')));
    }

    function admin_scripts()
    {
        wp_enqueue_style('betplugin-admin-css', BETPLUGIN_PLUGIN_URL . '/dist/admin.css', [], BETPLUGIN_VERSION, 'all');
    }

    function create_post_type()
    {
        $args = array(
            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => false,
            'show_in_menu' => false,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => ['custom-fields']
        );

        register_post_type('em_bet_teams', $args);
        register_post_type('em_bet_leagues', $args);
        register_post_type('em_bet_matches', $args);
        register_post_type('em_bet_bets', $args);

        new Seeder;
    }
}
