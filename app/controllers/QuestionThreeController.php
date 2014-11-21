<?php
use Illuminate\Http\Response as Respond;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class QuestionThreeController
 *
 * Crawls and retrieves CNN news article info
 */
class QuestionThreeController extends BaseController
{
    public $topics = ['topstories', 'latest', 'entertainment'];
    public $baseRSSUrl = 'http://rss.cnn.com/rss/cnn_';
    public $rssUrlEnd = '.rss';
    public $supportedTopics = ['topstories', 'world', 'us', 'latest', 'allpolitics', 'crime', 'tech', 'health',
        'showbiz', 'travel', 'living', 'freevideo', 'studentnews', 'mostpopular'];

    private function getTopicUrl($topic)
    {
        return $this->baseRSSUrl . $topic . $this->rssUrlEnd;
    }

    private function parseRequest($topics)
    {
        // Removes brackets
        $cleaned = str_replace('}', '', str_replace('{', '', $topics));

        /**
         * Creates an array from the string and maps values to integers. Not really necessary for the data
         * to be set as integers, I just think the output is cleaner/easier to work with.
         */
        $topics = explode(',', $cleaned);

        return $topics;
    }

    /**
     * Gets the articles in the selected topics.
     *
     * Passing a custom query like ?customSearch=true&topics={latest,showbiz} will crawl your selected topics.
     * Possible topics: topstories, world, us, latest, allpolitics, crime, tech, health
     * showbiz, travel, living, freevideo, studentnews, mostpopular
     *
     * Categories on the homepage are: latest, topstories,
     * @param array $topics
     * @return Response
     */
    public function getTheNews()
    {
        $topics = Input::get('customSearch') === 'true' ?
            $this->parseRequest(Input::get('topics')) :
            ['topstories', 'latest', 'showbiz'];

        $allArticles = [];
        // Iterates through chosen topics to get story info
        foreach ($topics as $topic) {
            try {
                // Loads and parses XML data
                $file = file_get_contents($this->getTopicUrl($topic));
            } catch (Exception $e) {
                return Response::json([
                        'status' => "You've entered an invalid topic",
                        'message' => $e->getMessage(),
                        'topic' => $topic],
                    Respond::HTTP_BAD_REQUEST
                );
            }
            $xml = new SimpleXMLElement($file);

            // Converts Simple XML objects to an easier to work with Assoc. Array
            $xmlToArray = json_decode(json_encode($xml, true));

            $articles = $xmlToArray->channel->item;

            $cleanedArticles = [];
            // Cleaning the array (description has unwanted html)
            foreach ($articles as $article) {
                $cleanedDescription = explode('<div class="feedflare">', $article->description);

                // Index 1 is the rest of the string, all unwanted html
                unset($cleanedDescription[1]);

                $article->description = $cleanedDescription[0];

                array_push($cleanedArticles, $article);
            }
            $allArticles[$topic] = $cleanedArticles;
        }

        return Response::json($allArticles, Respond::HTTP_OK);
    }
}