<?php
use Illuminate\Http\Response as Respond;

/**
 * Class QuestionTwoController
 *
 *
 */
class QuestionTwoController extends BaseController
{
    /**
     * Checks an array for consecutive ascending or descending integers
     *
     * Sending a query like ?random=true will create a random array to check. False will run the array given in the
     * problem. Run takes precedence over query.
     *
     * To send your own array of numbers, use something like ?random=false&query={3,4,1,1,6,11,15,6,7,8,10,9,8,2,3}
     *
     * @return Response
     */
    public function checkConsecutive()
    {
        // URL Param
        $random = Input::get('random');

        // If random, create an array to check
        if ($random == 'true') {
            $numList = [];
            for ($i = 0; $i < 50; $i++) {
                array_push($numList, rand(1, 19));
            }
        } else {
            $query = Input::get('query');

            // Removes brackets
            $cleaned = str_replace('}', '', str_replace('{', '', $query));

            /**
             * Creates an array from the string and maps values to integers. Not really necessary for the data
             * to be set as integers, I just think the output is cleaner/easier to work with.
             */
            $numList = array_map('intval', explode(',', $cleaned));

            // For debugging, known answer
            // Indexes [7,8,9] and [11,12]
            //$numList = [3,4,1,1,6,11,15,6,7,8,10,9,8,2,8];
        }

        // Going to fill these arrays
        $ascending = [];
        $descending = [];

        // Iterates through $numList to determine where consecutive integers lie
        for ($i = 0; $i < count($numList) - 1; $i++) {
            switch ($numList[$i + 1]) {
                // If the next index's value is 1 less than current index's value
                case ($numList[$i] - 1):
                    $descending[$i] = $numList[$i];
                    /**
                     * Adds the next index to the array of results because otherwise the last integer of the
                     * consecutive span will be left out. Any already existing integers in the array will be
                     * overwritten so no duplicates will occur.
                     */
                    $descending[$i + 1] = $numList[$i + 1];
                    break;
                // If the next index's value is 1 more than the current index's value
                case ($numList[$i] + 1):
                    // Same logic as for descending
                    $ascending[$i] = $numList[$i];
                    $ascending[$i + 1] = $numList[$i + 1];
                    break;
            }
        }

        return Response::json([
            'numbers' => $numList,
            'ascending' => $ascending,
            'descending' => $descending
        ],
        Respond::HTTP_OK);
    }
}