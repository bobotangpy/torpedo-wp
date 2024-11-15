<?php

namespace Torpedo\Wp\Config;

use WP_Admin_Bar;

class AdminBarEnvironmentSelector
{
    const STYLE_HANDLE = 'tp-env-selector';

    public static function register()
    {
        add_action('admin_bar_menu', [self::class, 'addEnvironmentLinks'], 999);
        add_action('wp_enqueue_scripts', [self::class, 'onEnqueueScripts']);
        add_action('admin_enqueue_scripts', [self::class, 'onEnqueueScripts']);
    }

    public static function onEnqueueScripts()
    {
        wp_register_style(self::STYLE_HANDLE, false);
        wp_enqueue_style(self::STYLE_HANDLE);
        wp_add_inline_style(self::STYLE_HANDLE, self::getCss());
    }

    public static function addEnvironmentLinks(WP_Admin_Bar $wp_admin_bar)
    {
        global $post;

        $url = $_SERVER['REQUEST_URI'];

        $wp_admin_bar->add_node([
            'id' => 'torpedoAdminBarEnvLabel',
            'title' => 'Go to: ',
            'meta' => [
                'class' => 'torpedo-adminbar-label',
            ]
        ]);

        $envs = [
            'development' => [
                'id'    => 'torpedoAdminBarEnvLocal',
                'title' => 'Local',
                'href'  => self::switchEnvironment($url, 'development'),
                'meta'  => [
                    'class' => 'torpedo-adminbar-env env-local',
                    'title' => 'View this page on local dev'
                ]
            ],
            'staging' => [
                'id'    => 'torpedoAdminBarEnvStaging',
                'title' => 'Staging',
                'href'  => self::switchEnvironment($url, 'staging'),
                'meta'  => [
                    'class' => 'torpedo-adminbar-env env-staging',
                    'title' => 'View this page on staging'
                ]
            ],
            'production' => [
                'id'    => 'torpedoAdminBarEnvProduction',
                'title' => 'Production',
                'href'  => self::switchEnvironment($url, 'production'),
                'meta'  => [
                    'class' => 'torpedo-adminbar-env env-production',
                    'title' => 'View this page on production'
                ]
            ],
        ];

        $envs[WP_ENV]['meta']['class'] .= ' env-current';

        foreach ($envs as $env => $args) {
            if ($env == 'development' && !current_user_can('administrator')) {
                continue;
            }

            if (!empty($args['href'])) {
                $wp_admin_bar->add_node($args);
            }
        }

        return $wp_admin_bar;
    }

    public static function switchEnvironment($uri, $environment)
    {
        $envVar = strtoupper("ENV_{$environment}_HOME");
        $url = getenv($envVar);
        if ($url) {
            return $url . $uri;
        }
        return '';
    }

    public static function getCss()
    {
        return
<<<CSS

            #wpadminbar .torpedo-adminbar-env {
                padding: 4px 0;
            }
            
            #wpadminbar .torpedo-adminbar-label {
                padding-left: 20px;                                   
            }
             
            #wpadminbar .torpedo-adminbar-env a {
                height: 24px; 
                color: black !important;
                font-weight: bold;                
                border-radius: 10px;
                padding: 0 14px !important;
                margin-right: 10px !important;
                line-height: 24px; 
                opacity: 0.4;     
            }
            
            #wpadminbar .torpedo-adminbar-env a:hover {
                opacity: 1.0 !important;
            }
            
            #wpadminbar .torpedo-adminbar-env.env-current a {
                font-weight: bold;                
                opacity: 1.0;
                color: white !important;
            }                      
            
            #wpadminbar .torpedo-adminbar-env.env-local a, 
            #wpadminbar .torpedo-adminbar-env.env-local a:hover 
            {
                background-color: #ad50d1 !important;
            }
            
            #wpadminbar .torpedo-adminbar-env.env-staging a,
            #wpadminbar .torpedo-adminbar-env.env-staging a:hover
            {
                background-color: #dea54d !important;
            }
            
            #wpadminbar .torpedo-adminbar-env.env-production a,
            #wpadminbar .torpedo-adminbar-env.env-production a:hover
            {
                background-color: #d74e4e !important;
            }                        
CSS;
    }
}

