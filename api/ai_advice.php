<?php
// api/ai_advice.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // 允许跨域调用

// ================= 配置区域 =================
// 你使用的是 V3 版本的 Key，直接填在这里即可，不需要 Secret Key
$API_KEY = "bce-v3/ALTAK-KtDgaXPTrp8X0V8XFnIOJ/9a2c7855da86b20682851a08027ca5fd40b8bc6c"; 
// 例如：bce-v3/ALTAK-KtDgaXPTrp8X0V8XFnIOJ/9a2c7855da86b20682851a08027ca5fd40b8bc6c
// ===========================================

// 1. 获取前端传来的错题数据
$input = json_decode(file_get_contents('php://input'), true);
$wrongTopics = isset($input['topics']) ? $input['topics'] : '';

if (empty($wrongTopics)) {
    echo json_encode(['status' => 'success', 'advice' => '恭喜你！本次考试没有明显的知识盲区，请继续保持！']);
    exit;
}

// 2. 构造给 AI 的提示词 (Prompt)
$prompt = "你是一位企业安全培训专家。员工在刚才的考试中，以下知识点或题目回答错误：\n" . 
          $wrongTopics . 
          "\n\n请根据这些错题，用鼓励且专业的语气，给出3条简短的学习建议，告诉他应该重点加强哪方面的安全知识。字数控制在200字以内。";

// 3. 调用百度千帆 V2/V3 兼容接口 (OpenAI 兼容模式)
// 注意：使用 bce-v3 Key 时，接口地址要变，且不需要获取 Access Token
$apiUrl = "https://qianfan.baidubce.com/v2/chat/completions";

$payload = json_encode([
    "model" => "ernie-speed-128k", // 指定模型，ERNIE Speed 速度快且免费额度多
    "messages" => [
        ["role" => "user", "content" => $prompt]
    ],
    "temperature" => 0.7
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// 【关键修改】V3 Key 直接放在 Header 的 Authorization 字段中
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $API_KEY // 这里的 Bearer 后面有个空格，千万别漏了
]);

$resultRaw = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['status' => 'error', 'advice' => '请求失败: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

$resultData = json_decode($resultRaw, true);

// 4. 解析结果 (兼容 V2 接口的返回格式)
if (isset($resultData['choices'][0]['message']['content'])) {
    // 成功获取建议
    echo json_encode(['status' => 'success', 'advice' => $resultData['choices'][0]['message']['content']]);
} elseif (isset($resultData['error'])) {
    // 百度返回了错误信息
    echo json_encode(['status' => 'error', 'advice' => 'AI分析失败: ' . $resultData['error']['message']]);
} else {
    // 其他未知错误
    echo json_encode(['status' => 'error', 'advice' => 'AI 响应格式异常，请稍后再试。']);
}
?>