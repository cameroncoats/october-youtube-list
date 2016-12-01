<?php namespace cameroncoats\YouTube;

use System\Classes\PluginBase;

/**
 * YouTube Videos plugin
 *
 * @author Brendon Park
 * @author modified: Cameron Coats
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Youtube List',
            'description' => 'Provides a component to display YouTube videos from a defined list',
            'author'      => 'Cameron Coats',
            'icon'        => 'icon-youtube-play',
            'homepage'    => 'https://github.com/cameroncoats/october-youtube-list'
        ];
    }

    /**
     * Register the settings listing
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'YouTube',
                'icon'        => 'icon-youtube-play',
                'description' => 'Configure YouTube API Key and Video settings',
                'class'       => 'cameroncoats\YouTube\Models\Settings',
                'order'       => 600
            ]
        ];
    }

    /**
     * Register the component/s
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            '\cameroncoats\YouTube\Components\VideoList' => 'VideoList'
        ];
    }

}
