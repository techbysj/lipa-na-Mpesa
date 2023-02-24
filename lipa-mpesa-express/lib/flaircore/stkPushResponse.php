<?php
/**
 * Returns the stk push reponse(s)
 */


class stkPushResponse
{
    /**
     * returns the stkPush response, log this and decide what to do
     * with the data, either discard if the user canceled the request,
     * or a timeout and/or post to the database if it was a success.
     * HINT: if you're extending this class and want to use the data
     * from the response you might consider assigning an extra security
     * token in your stk push CallBackURL and comparing it with the one
     * you get from the url response before calling this class/method
     */
    public function stkPushResponseData()
    {
        date_default_timezone_set('Africa/Dar_es_Salaam');

        $stkCallbackResponse = file_get_contents('php://input');

        return $stkCallbackResponse;

    }

}