<?php
/**
 * Created by PhpStorm.
 * User: wernerroets
 * Date: 2018/10/09
 * Time: 09:48
 */


function add_aweza_term_query_vars()
{
    $vars[] = 'aweza_term';
    return $vars;
}

add_filter('query_vars', 'add_aweza_term_query_vars');


function add_aweza_term_rewrite_rule()
{
    if (wp_get_current_user()->exists()) {
        add_rewrite_rule(
            '^aweza/term/([0-9]+)?',
            'index.php?aweza_term=$matches[1]',
            'top');
    }
}

add_action('init', 'add_aweza_term_rewrite_rule');

function aweza_term_request($wp)
{
    if (!empty($wp->query_vars['aweza_term'])) {
        $term_id = $wp->query_vars['aweza_term'];

        aweza_fetch_term($term_id);
        exit;
    }
}

function aweza_fetch_term($term_id)
{

    header('Content-Type: application/json');
    if (!is_user_logged_in()) {
        status_header(401);
        die(json_encode(['error' => 'Not logged into WordPress']));
    }
    $key = get_option('aweza_options')['aweza_key'];
    $secret = get_option('aweza_options')['aweza_secret'];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://tms2.aweza.co.za/api/term/' . $term_id);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['AWEZA-KEY: ' . $key, 'AWEZA-SECRET: ' . $secret]);
    curl_exec($curl);
}

add_action('parse_request', 'aweza_term_request');