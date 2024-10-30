<?php

namespace BetPlugin\Controllers;

use stdClass;

class Leagues
{
    function create($data)
    {
        $new_post = array(
            'post_title' => '',
            'post_type' => 'em_bet_leagues',
            'post_status' => 'publish'
        );

        $new_post_id = wp_insert_post($new_post);

        // Almacenar información en el campo personalizado
        update_post_meta($new_post_id, 'data', json_encode($data));
        return $new_post_id;
    }

    function read($id = null)
    {
        if ($id === null) {
            $args = array(
                'post_type' => 'em_bet_leagues',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            );
            $posts = get_posts($args);

            $array = [];
            foreach ($posts as $post) {
                $data = json_decode(get_post_meta($post->ID, 'data', true), true);
                $array[] = [
                    "id" => $post->ID,
                    "name" => isset($data["name"]) ? $data["name"] : "",
                ];
            }
            return $array;
        } else {
            // ID del post que deseas recuperar
            $posts = get_post($id);
            if ($posts == null) {
                return [
                    "id" => "",
                    "name" => "",
                ];
            }
            $data = json_decode(get_post_meta($posts->ID, 'data', true), true);

            return [
                "id" => $posts->ID,
                "name" => isset($data["name"]) ? $data["name"] : "",
            ];
        }
    }

    function update($id, $data)
    {
        update_post_meta($id, 'data', json_encode($data));
    }

    function delete($id)
    {
        wp_delete_post($id);
    }
}
