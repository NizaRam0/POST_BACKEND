<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use OpenAI\Factory;


class OpenAiService
{
    /**
     * Create a new class instance.
     */
    public function generatePromptForImage(UploadedFile $image): string
    {
         $imageData= base64_encode(file_get_contents($image->getPathname()));
         $mimeType=$image->getMimeType();
         $client=(new Factory())->withApiKey(config('services.openai.api_key'))->make();
         $response=$client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role'=>'system',
                    'content'=>'You are a helpful assistant that generates descriptive prompts for images.'
                ],
                [
                    'role' => 'user',
                    'content' =>[
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$imageData}"
                            ]
                        ],
                        [
                            'type' => 'text',
                            'text' => "Analyze this image and generate a detailed, descriptive
                            prompt that could be used to recreate a similar image with AI image 
                            generation tools. The prompt should be comprehensive, describing the 
                            visual elements, style, composition, lighting, colors, and any other 
                            relevant details. Make it detailed enough that someone could use it 
                            to generate a similar image. You MUST preserve aspect ratio exact as 
                            the original image has or very close to it. "
                        ]
                    ]
                ]
            ]
         ]);
         return $response->choices[0]->message->content;
   
         }
}
