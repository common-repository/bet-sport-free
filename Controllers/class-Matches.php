<?php

namespace BetPlugin\Controllers;

class Matches
{
    private $leagueController;
    private $teamController;

    function __construct()
    {
        $this->leagueController = new Leagues;
        $this->teamController = new Teams;
    }
    function create($data)
    {
        unset($data["id"]);
        $new_post = array(
            'post_title' => '',
            'post_type' => 'em_bet_matches',
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
                'post_type' => 'em_bet_matches',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            );

            $posts = get_posts($args);

            $array = [];
            foreach ($posts as $post) {
                $data = json_decode(get_post_meta($post->ID, 'data', true), true);
                $array[] = [
                    "id" => $post->ID,
                    "league" => $this->leagueController->read($data["league_id"]),
                    "team1" => $this->teamController->read($data["team1_id"]),
                    "team2" => $this->teamController->read($data["team2_id"]),
                    "date" => isset($data["date"]) ? $data["date"] : "",
                    "stage" => isset($data["stage"]) ? $data["stage"] : "",
                    "stadium" => isset($data["stadium"]) ? $data["stadium"] : "",
                    "score" => isset($data["score"]) ? $data["score"] : "",
                ];

                // $data = get_post_meta($post->ID, 'data', true) === '' ? [] : json_decode(get_post_meta($post->ID, 'data', true), true);
                // $array[] = array_merge(["id" => $post->ID], $data);
            }
            return $array;
        } else {

            // ID del post que deseas recuperar
            $posts = get_post($id);
            $data = json_decode(get_post_meta($posts->ID, 'data', true), true);

            return [
                "id" => $posts->ID,
                "league" => $this->leagueController->read($data["league_id"]),
                "team1" => $this->teamController->read($data["team1_id"]),
                "team2" => $this->teamController->read($data["team2_id"]),
                "date" => isset($data["date"]) ? $data["date"] : "",
                "stage" => isset($data["stage"]) ? $data["stage"] : "",
                "stadium" => isset($data["stadium"]) ? $data["stadium"] : "",
                "score" => isset($data["score"]) ? $data["score"] : "",
            ];
        }
    }

    function update($id, $data)
    {
        unset($data["id"]);
        update_post_meta($id, 'data', json_encode($data));
    }

    function delete($id)
    {
        wp_delete_post($id);
    }
}
