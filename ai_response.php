<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents("php://input"), true)['input'];

    $apiKey = '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';  // Use your Hugging Face API key

    $data = [
        'inputs' => $input,
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api-inference.huggingface.co/models/facebook/blenderbot-400M-distill",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ),
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo json_encode(['response' => "cURL Error #:" . $err]);
    } else {
        $data = json_decode($response, true);
        $aiResponse = isset($data['generated_text']) ? $data['generated_text'] : 'No response';
        echo json_encode(['response' => $aiResponse]);
    }
}
?>
