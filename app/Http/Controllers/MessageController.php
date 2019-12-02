<?php

namespace App\Http\Controllers;

use App\Message;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $cities = ['Damascus','Mogadishu','Ibiza','Cairo','Tahrir','Nairobi','Kathmandu','Madrid','Athens','Istanbul'];
        $client = new Client();
        $feed = $client->get('https://spreadsheets.google.com/feeds/list/0Ai2EnLApq68edEVRNU0xdW9QX1BqQXhHRl9sWDNfQXc/od6/public/basic?alt=json', [])->getBody()->getContents();
        $data = json_decode($feed);
        $result = array();
        $Neutral = array();
        $Negative = array();
        $Positive = array();
        foreach ($data->feed->entry as $entry)
        {
            $messages = $entry->content->{'$t'};
            $time = $entry->title->{'$t'};
            $explods = explode(",", $messages);
            if (sizeof($explods) == 3 ){
                $message = str_replace('message: ','',$explods[1]);
                $sentiment = str_replace('sentiment: ','',$explods[2]);
                foreach ($cities as $city)
                {
                    if (strpos($message, $city) !== false) {
                        $location = $city;
                    }
                }
                array_push($result,[
                    'message'=>$message,
                    'sentiment'=>$sentiment,
                    'time' =>$time,
                    'location'=>$location
                ]);
            }
            else
            {
                $append="";
                for($i=1 ; $i<=array_key_last($explods)-1; $i++){
                    $append.=$explods[$i];
                    $message = str_replace('message: ','',$append);
                }
                $sentiment = str_replace('sentiment: ','',$explods[array_key_last($explods)]);
                foreach ($cities as $city)
                {
                    if (strpos($message, $city) !== false) {
                        $location = $city;
                    }
                }
                array_push($result,[
                    'message'=>$message,
                    'sentiment'=>$sentiment,
                    'time' =>$time,
                    'location'=>$location
                ]);

            }

        }
        foreach ($result as $data)
        {
//            dd($data['sentiment']);
            if ($data['sentiment'] == ' Neutral'){
                array_push($Neutral,$data);
            }
            elseif($data['sentiment'] == ' Positive')
            {
                array_push($Positive,$data);
            }
            else
            {
                array_push($Negative,$data);

            }
        }
        return view('welcome')->with(['Neutral'=>$Neutral , 'Positive'=>$Positive ,'Negative'=>$Negative]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
