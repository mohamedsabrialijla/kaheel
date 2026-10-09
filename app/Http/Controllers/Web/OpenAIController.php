<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

  


class OpenAIController extends Controller
{
    



    public function checkText(Request $request)
{
    $text = $request->input('text');

    if (!$text) {
        return response()->json(['error' => 'Text is required.'], 400);
    }

    $openaiKey = env('OPENAI_API_KEY');


    if (!$openaiKey) {
        return response()->json(['error' => 'OpenAI API key not configured.'], 500);
    }
  
    // تحديد اللغة تلقائيًا
    $lang = (preg_match('/\p{Arabic}/u', $text)) ? 'Arabic' : 'English';

    $response = Http::withOptions([
        'verify' => false,
    ])->withHeaders([
        'Authorization' => 'Bearer ' . $openaiKey,
        'Content-Type' => 'application/json',
    ])->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant who corrects grammar. Return only the corrected version of the text, nothing else.'],
            ['role' => 'user', 'content' => 'Please correct the following text in ' . $lang . ': ' . $text],
        ],
        'max_tokens' => 1000,
        'temperature' => 0.2,
    ]);

    $result = $response->json();

/*    return response()->json($result);
*/
    if (isset($result['choices'][0]['message']['content'])) {
        return response()->json([
            'corrected_text' => trim($result['choices'][0]['message']['content'])
        ]);
    }

    return response()->json(['error' => 'Something went wrong with the AI response.'], 500);
}



    public function rephraseText(Request $request)
    {
        $text = $request->input('text');

        if (!$text) {
            return response()->json(['error' => 'Text is required.'], 400);
        }

        $openaiKey = env('OPENAI_API_KEY');

        if (!$openaiKey) {
            return response()->json(['error' => 'OpenAI API key not configured.'], 500);
        }

        // تحديد اللغة بناءً على اللغة الحالية في التطبيق
        $lang = app()->getLocale() === 'en' ? 'English' : 'Arabic';

        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $openaiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant who rephrases text. Return only the rephrased version of the text, nothing else.'],
                ['role' => 'user', 'content' => 'Please rephrase the following text in ' . $lang . ': ' . $text],
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ]);

        $result = $response->json();

        if (isset($result['choices'][0]['message']['content'])) {
            return response()->json([
                'rephrased_text' => trim($result['choices'][0]['message']['content'])
            ]);
        }

        return response()->json(['error' => 'Something went wrong with the AI response.'], 500);
    }








    public function Suggestions(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'type' => 'required|string'
        ]);

        $text = $request->input('text');
        $type = $request->input('type');

        $openaiKey = env('OPENAI_API_KEY');

        if (!$openaiKey) {
            return response()->json(['error' => 'OpenAI API key not configured.'], 500);
        }

        // تحديد اللغة تلقائيًا
        $lang = (preg_match('/\p{Arabic}/u', $text)) ? 'Arabic' : 'English';

        // إعداد التعليمات حسب النوع واللغة
        if ($type === 'summary') {
            $instruction = ($lang === 'Arabic') ?
                "أريد منك أن تكتب لي فقرة موجزة بصيغة احترافية تصلح لقسم (نبذة عني) في السيرة الذاتية. سأرسل لك المسمى الوظيفي وبعض المعلومات، وأنت فقط ارجع بفقرة واحدة مناسبة واحترافية، بدون أي مقدمات أو شرح." :
                "Write a concise and professional paragraph suitable for the 'About Me' section in a resume. I will provide the job title and some context. Respond with only one clean paragraph, no explanations.";
        } else {
            $instruction = ($lang === 'Arabic') ?
                "أريد منك أن ترجع لي 10 اقتراحات بصيغة مصفوفة JSON صافية. لا تستخدم الترقيم. لا تضع الشرح. لا تستخدم Markdown. فقط أرجع المصفوفة كالتالي:\n[\"html\", \"css\", \"javascript\", \"git\", \"react\", \"node.js\"]\nتأكد من أن الإخراج هو مصفوفة JSON فقط بدون أي إضافات، لا نصوص، لا رموز، لا شروحات." :
                "Return exactly 10 suggestions as a clean JSON array only. Do not include numbering. Do not explain anything. Do not use Markdown formatting (no triple backticks). Just return the JSON array like this:\n[\"html\", \"css\", \"javascript\", \"git\", \"react\", \"node.js\"]\nThe output must be a valid JSON array only. No text, no symbols, no explanations.";

        }

        $fullPrompt = $instruction . "\n\n" . $text;

        try {
            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $openaiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a resume suggestion assistant.'],
                    ['role' => 'user', 'content' => $fullPrompt],
                ],
                'max_tokens' => 300,
                'temperature' => 0.3,
            ]);

            $result = $response->json();

            if (isset($result['choices'][0]['message']['content'])) {
                $raw = trim($result['choices'][0]['message']['content']);
                if ($type === 'summary') {
                    // إرجاع الفقرة كما هي
                    return response()->json([
                        'original_text' => $text,
                        'suggestions' => $raw
                    ]);
                } else {
                    // محاولة تحويل المصفوفة من JSON
                    $cleaned = str_replace(['```json', '```'], '', $raw);

                    try {
                        $parsed = json_decode($cleaned, true);
                    

                        return response()->json([
                            'original_text' => $text,
                            'suggestions' => $parsed
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'error' => 'فشل تحويل الرد إلى JSON: ' . $e->getMessage(),
                            'raw_response' => $raw
                        ], 500);
                    }
                }
            }

            return response()->json(['error' => 'رد غير متوقع من OpenAI.'], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ أثناء الاتصال بـ OpenAI API: ' . $e->getMessage()
            ], 500);
        }
    }


    public function checkAts(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $text = $request->input('text');

        $openaiKey = env('OPENAI_API_KEY');

        if (!$openaiKey) {
            return response()->json(['error' => 'OpenAI API key not configured.'], 500);
        }

        $lang = (preg_match('/\p{Arabic}/u', $text)) ? 'Arabic' : 'English';

        $instruction = ($lang === 'Arabic') ?
            "أنت خبير في تحليل السير الذاتية بناءً على نظام ATS. راجع السيرة الذاتية التالية وأعد تقييمًا مفصلًا. أريد منك أن ترجع لي JSON يحتوي على:
            1. النسبة المئوية لقوة السيرة الذاتية (مثل: 82)
            2. مصفوفة تحتوي على أقسام مختلفة (مثل 'المهارات', 'الخبرات', 'الملخص'), وكل قسم يحتوي على مصفوفة من الملاحظات أو المشاكل.

            مثال على الإخراج:
            {
              \"score\": 82,
              \"sections\": {
                \"الملخص\": [\"الملخص قصير جدًا\", \"غير مخصص لوظيفة معينة\"],
                \"المهارات\": [\"لا توجد مهارات تقنية محددة\"],
                \"الخبرات\": [\"لا يوجد تسلسل زمني واضح\"]
              }
            }

            فقط أعد هذا الشكل بدون أي شرح أو نص إضافي." :
            "You are an expert in ATS resume evaluation. Review the following resume and return a JSON object with:
            1. A score (percentage) like 82
            2. A 'sections' object where each key is a section name (like 'Summary', 'Skills') and value is an array of issues or notes for that section.

            Example:
            {
              \"score\": 82,
              \"sections\": {
                \"Summary\": [\"Too generic\", \"Missing keywords\"],
                \"Skills\": [\"Not enough technical skills listed\"],
                \"Experience\": [\"No dates included\"]
              }
            }

            Only return the JSON. No explanations.";

        $fullPrompt = $instruction . "\n\n" . $text;

        $response = Http::withOptions([
            'verify' => false,
            'timeout' => 30,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $openaiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an ATS evaluation assistant. Return only the JSON structure.'],
                ['role' => 'user', 'content' => $fullPrompt],
            ],
            'max_tokens' => 1500,
            'temperature' => 0.3,
        ]);

        $result = $response->json();

        if (isset($result['choices'][0]['message']['content'])) {
            $raw = trim($result['choices'][0]['message']['content']);
            $cleaned = str_replace(['```json', '```'], '', $raw);

            try {
                $parsed = json_decode($cleaned, true);

                return response()->json([
                    'original_text' => $text,
                    'score' => $parsed['score'] ?? null,
                    'sections' => $parsed['sections'] ?? null,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'فشل في تحويل الرد إلى JSON: ' . $e->getMessage(),
                    'raw_response' => $raw
                ], 500);
            }
        }

        return response()->json(['error' => 'No response from AI or unexpected structure.'], 500);
    }

















    //Gemin function






    // public function checkText(Request $request)
    // {
    //     $request->validate([
    //         'text' => 'required|string'
    //     ]);

    //     $text = $request->input('text');

    //     $client = new Client();

    //     try {
    //         $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key='. env('GEMINI_API_KEY') , [
    //             'json' => [
    //                 'contents' => [
    //                     [
    //                         'parts' => [
    //                             [
    //                                 'text' => 'قم بتدقيق النص التالي إملائياً: ورجع النص المدقق فقط بدون اي حرف اخر '.$text
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . env('GEMINI_API_KEY')
    //             ],
    //             'verify' => false 
    //         ]);

    //         $data = json_decode($response->getBody()->getContents(), true);

    //         $correctedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'خطأ في التصحيح';


    //         $correctedText = trim($correctedText); 

    //         return response()->json([
    //             'original_text' => $text,
    //             'corrected_text' => $correctedText
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'حدث خطأ أثناء الاتصال بـ API: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }




    // public function rephraseText(Request $request)
    // {
    //     $request->validate([
    //         'text' => 'required|string'
    //     ]);

    //     $text = $request->input('text');

    //     $client = new Client();

    //     try {
    //         $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=AIzaSyCMmdLTjFG3aESK8rExqxtEyCnh_Q1sbUY', [
    //             'json' => [
    //                 'contents' => [
    //                     [
    //                         'parts' => [
    //                             [
    //                                 'text' => 'قم باعادة صياغة النص التالي حسب لغة النص القادمة اذا كانت عربي بالعربي اذا كانت انجليزي بالانجليزي : ورجع النص المصاغ فقط بدون اي حرف اخر '.$text
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . env('GEMINI_API_KEY')
    //             ],
    //             'verify' => false 
    //         ]);

    //         $data = json_decode($response->getBody()->getContents(), true);

    //         $correctedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'خطأ في التصحيح';


    //         $correctedText = trim($correctedText); 

    //         return response()->json([
    //             'original_text' => $text,
    //             'corrected_text' => $correctedText
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'حدث خطأ أثناء الاتصال بـ API: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }



    // public function checkAts(Request $request)
    // {
    //     $request->validate([
    //         'text' => 'required|string'
    //     ]);

    //     $text = $request->input('text');

    //     $client = new Client();


    //     $text2 = "راجع السيرة الذاتية التالية وقيمها بناءً على متطلبات ATS. أريد منك أن ترجع لي مصفوفة تحتوي على العنصر الأول وهو نسبة قوة السيرة الذاتية (كنسبة مئوية)، يليها مجموعة من المصفوفات الفرعية. كل مصفوفة فرعية يجب أن تحتوي على اسم القسم في السيرة الذاتية كمفتاح 'key' مع مصفوفة من المشكلات المرتبطة بهذا القسم كقيمة 'value'. يجب أن يكون التنسيق على شكل مصفوفات فقط بدون أي نصوص إضافية اذا ارسلت اليك باللغة العربية رجعلي باللغة الربية واذا بالانجليزية ارجعلي بالانجليزية.

    //         البيانات ستكون على النحو التالي:
    //         [
    //             85,
    //             [
    //                 {
    //                     'الملخص الشخصي': [
    //                         'طويل جداً (يفضل أن يكون أقصر وأكثر تركيزاً على الكلمات المفتاحية)',
    //                         'يمكن تحسين استخدام الكلمات المفتاحية المتعلقة بمتطلبات الوظيفة المستهدفة'
    //                     ]
    //                 },
    //                 {
    //                     'المهارات الأساسية': [
    //                         'يجب تصنيف المهارات إلى فئات مثل Back-end و Front-end',
    //                         'يجب توضيح مستوى الخبرة في كل مهارة'
    //                     ]
    //                 },
    //                 {
    //                     'الخبرات العملية': [
    //                         'يجب توضيح حجم الفريق الذي قاده أو عمل معه',
    //                         'يجب إضافة مقاييس قابلة للقياس (أرقام، نسب مئوية) لإبراز الإنجازات'
    //                     ]
    //                 },
    //                 {
    //                     'الشهادات': [
    //                         'يفضل ذكر تاريخ الحصول على الشهادات',
    //                         'ممتاز وجود الشهادات'
    //                     ]
    //                 }
    //             ]
    //         ]";






    //     try {
    //         $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=AIzaSyCMmdLTjFG3aESK8rExqxtEyCnh_Q1sbUY', [
    //             'json' => [
    //                 'contents' => [
    //                     [
    //                         'parts' => [
    //                             ['text' => $text2.$text

    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . env('GEMINI_API_KEY')
    //             ],
    //             'verify' => false 
    //         ]);

    //         $data = json_decode($response->getBody()->getContents(), true);

    //         $correctedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'خطأ في التصحيح';


    //         $correctedText = trim($correctedText);


    //         $cleanedText = str_replace('```json', '', $correctedText);
    //         $cleanedText = str_replace('```', '', $cleanedText);
            

          
    //         return response()->json([
    //             'original_text' => $text,
    //             'corrected_text' => json_decode($cleanedText)
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'حدث خطأ أثناء الاتصال بـ API: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }






    // public function Suggestions(Request $request)
    // {
    //     $request->validate([
    //         'text' => 'required|string'
    //     ]);


    //     $client = new Client();

    //     $instruction = 'أريد منك أن ترجع لي 10 اقتراحات بصيغة مصفوفة فقط، بدون أي نصوص إضافية. سوف أرسل لك اسم الوظيفة (Job Title) بالإضافة إلى القسم (مثل: مهارات، خبرات، نبذة عني). 
    //     إذا أرسلت لك النص باللغة العربية، أرجع لي النتيجة بالعربية، وإذا بالإنجليزية، أرجع بالإنجليزية.
    //      مثل هذه المصفوفة بدون زيادات وكلام وشرح اريد فقط النقاط
    //    كل عنصر في المصفوفة يجب أن يكون اقتراحًا مرتبطًا بالمسمى الوظيفي والقسم.
    //        ارجوك اريدها نقاط بسيكة داخل الاري بدون زيادة او شرح بالكلام 
    //        ولا اريد تقسم بالمهارات ولا تعداد اريدها مصفوفة فيها 10 قيم
    //         [
    //             "html",
    //             "css",
    //             "javascript",
    //             "git",
    //             "react",
    //             "node.js"
    //           ]

    //         ';

    //         $userPrompt = $request->text;




    //         try {
    //             $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=AIzaSyCMmdLTjFG3aESK8rExqxtEyCnh_Q1sbUY', [
    //                 'json' => [
    //                     'contents' => [
    //                         [
    //                             'parts' => [
    //                                 [
    //                                     'text' => $instruction,
    //                                     'text' => $userPrompt

    //                                 ]
    //                             ]
    //                         ]
    //                     ]
    //                 ],
    //                 'headers' => [
    //                     'Authorization' => 'Bearer ' . env('GEMINI_API_KEY')
    //                 ],
    //                 'verify' => false 
    //             ]);

    //             // dd($response->getBody()->getContents());


    //             $data = json_decode($response->getBody()->getContents(), true);

    //             $correctedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No content';


                


    //             $correctedText = trim($correctedText);

                


    //             $cleanedText = str_replace('```json', '', $correctedText);
    //             $cleanedText = str_replace('```', '', $cleanedText);
                

              
    //             return response()->json([
    //                 'original_text' => $userPrompt,
    //                 'corrected_text' => json_decode($cleanedText)
    //             ], 200);

    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'error' => 'حدث خطأ أثناء الاتصال بـ API: ' . $e->getMessage()
    //             ], 500);
    //         }
    // }









}
 