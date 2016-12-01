<?php namespace Cameroncoats\YouTube\Classes;

use Google_Client;
use Cameroncoats\YouTube\Models\Settings;
use Carbon\Carbon;
use October\Rain\Exception\ApplicationException;

/**
 * YouTube API Client class
 *
 * @author Brendon Park
 * @author modified: Cameron Coats
 *
 */
class YouTubeClient
{

    use \October\Rain\Support\Traits\Singleton;

    /**
     * @var Google_Client Google API Client
     */
    public $client;
    public $service;

    protected function init()
    {
        $settings = Settings::instance();

        if (!strlen($settings->api_key))
            throw new ApplicationException('Google API access requires an API Key. Please add your key to Settings / Misc / YouTube');

        // Create the Google Client
        $client = new Google_Client();
        $client->setDeveloperKey($settings->api_key);
        $this->client = $client;
        $this->service = new \Google_Service_YouTube($client);
    }

    /**
     * Grabs videos from an array of video IDs
     *
     * @param $videoIDArray array of video IDs to fetch
     * @param $maxItems int maximum number of items to display
     * @param $thumbResolution string Thumbnail resolution (default, medium, high)
     * @return array|null array of videos or null if failure
     */
    public function getList($videoIDArray, $maxItems = 12, $thumbResolution = 'medium')
    {
        try {
            // Build the query and submit it
            $videoIDsOnly = array();
            $categoriesByID = array();
            foreach($videoIDArray as $vid){
                $videoIDsOnly[] = $vid['video_id'];
                $categoriesByID[$vid['video_id']] = $vid['category'];
            }
            $params = array('id' => implode(',',$videoIDsOnly),
                'maxResults' => $maxItems);
            $results = $this->service->videos->listVideos('statistics,snippet', $params);

            // Parse the results
            $videos = [];
            foreach ($results['items'] as $item) {

                if ($item['kind'] != 'youtube#video') {
                    continue;
                }

                // Get the desired thumbnail resolution, YouTube's API doesn't support a proper high-res thumbnail
                $thumbnails = $item['snippet']['thumbnails'];
                switch($thumbResolution)
                {
                    case 'full-resolution':
                        $thumbnail = 'https://img.youtube.com/vi/' . $item['id'] . '/maxresdefault.jpg';
                        break;
                    case 'default':
                        $thumbnail = $thumbnails['default']['url'];
                        break;
                    case 'medium':
                        $thumbnail = $thumbnails['medium']['url'];
                        break;
                    case 'high':
                        $thumbnail = $thumbnails['high']['url'];
                        break;
                    default:
                        $thumbnail = $thumbnails['default']['url'];
                        break;
                }

                array_push($videos, array(
                    'id'            => $item['id'],
                    'link'          => 'https://youtube.com/watch?v=' . $item['id'],
                    'title'         => $item['snippet']['tittle'],
                    'views'         => $item['statistics']['viewCount'],
                    'likes'         => $item['statistics']['likeCount'],
                    'thumbnail'     => $thumbnail,
                    'category'      => $categoriesByID[$item['id']],
                    'description'   => $item['snippet']['description'],
                    'published_at'  => Carbon::parse($item['snippet']['publishedAt'])
                ));
            }
            return $videos;
        }
        catch (\Exception $e)
        {
            // Since we're relying on an outside source, lets not crash the page if we can't reach YouTube
            traceLog($e);
            return null;
        }
    }

    public function getLatestCacheKey($videoIDArray, $maxItems, $thumbResolution)
    {
        // Components with the same channel and item count will use the same cached response
        // // Build the query and submit it
        $videoIDsOnly = array();
        foreach($videoIDArray as $vid){
            $videoIDsOnly[] = $vid['video_id'];
        }
        return 'cameroncoats_ytvideos_' . implode(',',$videoIDsOnly) . '_' . $maxItems . '_' . $thumbResolution;
    }

}
