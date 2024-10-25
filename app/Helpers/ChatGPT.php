<?php

namespace App\Helpers;

use OpenAI;
use function Laravel\Prompts\select;

class ChatGPT
{


    public static function ask($databaseSchema, $prompt)
    {
        $response = OpenAI::client(config('services.openai.key'))->chat()->create([
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Given the following MySQL tables, your job is to write queries given a user’s request. You must only return with SQL query. \n\n$databaseSchema\n);"
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            "temperature" => 0,
            "max_tokens" => 1024,
            "top_p" => 1,
            "frequency_penalty" => 0,
            "presence_penalty" => 0
        ]);

        return $response['choices'][0]['message']['content'];
    }
}
