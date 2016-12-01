<?php namespace Cameroncoats\YouTube\Components;

use Cache;
use Cameroncoats\YouTube\Models\Settings;
use Cms\Classes\ComponentBase;
use Cameroncoats\YouTube\Classes\YouTubeClient;


/**
 * Latest Videos component
 *
 * @author Brendon Park
 * @author modified: Cameron Coats
 *
 */
class Videos extends ComponentBase
{

    public $videos;

    public function componentDetails()
    {
        return [
            'name'        => 'Videos',
            'description' => 'Display a list of videos defined in settings'
        ];
    }

    public function defineProperties()
    {
        return [
            'max_items' => [
                'title' => 'Max Items',
                'description' => 'Maximum number of results',
                'default' => '12'
            ],
            'thumb_resolution' => [
                'title' => 'Thumbnail Size',
                'type' => 'dropdown',
                'description' => "Thumbnails may return cropped images as per the YouTube API.
                                    However, 'Full Resolution' may fail to find an image, but won't be cropped.",
                'default' => 'medium',
                'options' => [  'full-resolution' => 'Full Resolution',
                                'high' => 'High',
                                'medium' => 'Medium',
                                'default' => 'Default']
            ]
        ];
    }

    public function onRun()
    {
        $maxItems = $this->property('max_items');
        $thumbResolution = $this->property('thumb_resolution');
        $videoIDArray = Settings::get('videos');
        $cacheKey = YouTubeClient::instance()->getLatestCacheKey($videoIDArray, $maxItems, $thumbResolution);

        $this->videos = Cache::remember($cacheKey,
                                        Settings::get('cache_time'),
                                        function() use ($videoIDArray, $maxItems, $thumbResolution)
        {
            return  YouTubeClient::instance()->getLatest($videoIDArray, $maxItems, $thumbResolution);
        });
    }


}
