<?php
use Illuminate\Http\Response as Respond;

/**
 * Class QuestionOneController
 *
 * Controller for question one. It uses a mock database in the form of a JSON file in app/storage/mockdb/codes.json.
 * Any lines with print_r() are for debugging.
 */
class QuestionOneController extends BaseController
{
    private $dbPath = null;
    private $dbFile = null;

    /**
     * DB Info is in .env.local.php in the web app root directory.
     */
    public function __construct()
    {
        $this->dbPath = $_ENV['DB_PATH'];
        $this->dbFile = $this->dbPath . $_ENV['DB_FILE'];
    }

    /**
     * Reads the mockdb file and processes any query requests needed.
     *
     * @param bool $query
     * @param array $codesRequested
     * @return Response
     */
    private function getDatabaseData($query = false, $codesRequested = ['C', 'R'])
    {
        // If this were a real database then this wouldn't need to pull all the data we have on each request.
        $json = file_get_contents($this->dbFile);

        $data = json_decode($json);

        if ($query === false) {
            return $data;

        } elseif ($query === true) {
            $results = [];

            foreach ($codesRequested as $code) {
                $results[$code] = $data->$code;
            }
            return $results;
        }

        // Else...
        return Response::json(['status' => '400'], Respond::HTTP_BAD_REQUEST);
    }

    /**
     * Returns an array of just the code descriptors.
     *
     * Would normally be a private method. Public to see output.
     *
     * @return array $descriptors
     */
    public function getDescriptors()
    {
        $data = $this->getDatabaseData();

        $descriptors = [];
        foreach ($data as $key => $value) {
            $descriptors[$key] = $key;
        }

        return $descriptors;
    }

    /**
     * Encodes a string for easier comparisons
     *
     * Own idea but based code off
     * http://stackoverflow.com/questions/16855555/convert-alphanumeric-to-numeric-and-should-be-an-unique-number
     *
     * @param string $code
     * @return string
     *
     * @TODO If time, write a decode method
     */
    private function encode($code)
    {
        // Split string into array and flip keys/values
        $array = array_flip(str_split($code));

        // Remove dash and slash, if present
        if (strpos($code,'/') !== false) {
            unset($array['/']);
        }

        if (strpos($code,'-') !== false) {
            unset($array['-']);
        }

        $unflip = array_flip($array);
        $string = join('', $unflip);

        $rules = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        /**
         * Each character in $rules will now correspond to a number (originally the array index for that character.
         * Character is the key, number is value.
         */
        $rules = array_flip(str_split($rules));
        $result = [];
        $stringLength = strlen($string);

        /**
         * Each character in the string is saved to $rulesIndex to be used as an index to get the corresponding
         * character's value and changed back from base 35 (9 numbers + 26 letters).
         */
        for ($i = 0; $i < $stringLength; $i++) {
            $ruleIndex = $string[$stringLength - ($i + 1)];
            array_push($result, ($rules[$ruleIndex] * pow(35, $i)));
        }
        return join('', $result);
    }

    /**
     * Gets descriptions for an array of codes
     *
     * Accepts a url parameter in the format of query={R7A,R8A,C4-4A}
     *
     * @return Response
     */
    public function getDescriptions()
    {
        // Laravel automatically sanitizes inputs
        $request = Input::get('query');

        // Remove {} from string
        $cleaned = str_replace('{', '', $request);
        $cleanedTwice = str_replace('}', '', $cleaned);

        // Parse to array
        $codes = explode(',', $cleanedTwice);

        // Codes with no ranges
        $specialDescriptors = ['BPC', 'PARK', 'PARKNYS', 'PARKUS', 'ZNA', 'ZR'];

        /**
         * If valid codes were a necessity (to later have work done with them), then encasing this in a
         * try/catch and throwing an exception then handling it would prevent invalid codes from slipping through.
         */
        $results = [];
        foreach ($codes as $code) {
            switch ($code) {
                // If not a special code
                case (!in_array($code, $specialDescriptors)):
                    $encoded = $this->encode($code);

                    if (strpos($code, '/') !== false) {
                        $descriptor = 'M/R';
                    } else {
                        $descriptor = $code[0];
                    }

                    $results[$code] = $code[0] === 'R' ?
                        $this->checkRange($encoded, $descriptor, $code, true) :
                        $this->checkRange($encoded, $descriptor, $code);
                    break;
                // If a special code
                // @TODO Add a check for validity
                default:
                    $codeData = $this->getDatabaseData(true, [$code]);

                    $results[$code] = [
                        'description' => $codeData[$code]->description,
                        'code' => $code
                    ];
            }
        }

        return Response::json($results, Respond::HTTP_OK);
    }

    /**
     * Checks if the encoded code is within the accepted range
     *
     * @param string $encoded
     * @param string $descriptor
     * @param string $code
     * @return array
     */
    public function checkRange($encoded, $descriptor, $code, $twoRanges = false)
    {
        // Queries the mock database
        $codeData = $this->getDatabaseData(true, [$descriptor]);

        // Encode the range min and max
        $encodedMin = $this->encode($codeData[$descriptor]->range->min);
        $encodedMax = $this->encode($codeData[$descriptor]->range->max);

        // If two ranges (R descriptor)
        if ($codeData[$descriptor]->ranges == 2) {
            $rangeTwoMin = $this->encode($codeData[$descriptor]->range->twoMin);
            $rangeTwoMax = $this->encode($codeData[$descriptor]->range->twoMax);
        }

        // If matches first range...
        if (($encoded >= $encodedMin && $encoded <= $encodedMax)) {
            return [
                'code' => $code,
                'description' => $codeData[$descriptor]->description
            ];

            // For R, if matches second range...
        } elseif ($twoRanges === true && ($encoded >= $rangeTwoMin && $encoded <= $rangeTwoMax)) {
            return [
                'code' => $code,
                'description' => $codeData[$descriptor]->description
            ];
        }

        // Else..
        return [
            'status' => 'Not a valid code',
            'code' => $code
        ];
    }
}
