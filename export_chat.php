<?php
$backupPath = 'c:/xampp/htdocs/Profess0rPay/chat_history_backup.md';
$transcriptPath = 'C:/Users/PROFESSOR/.gemini/antigravity-ide/brain/9d3a7d97-3fa9-41a2-9e30-9bb7c44915ff/.system_generated/logs/transcript.jsonl';

$content = "\n\n---\n## Session: 9d3a7d97-3fa9-41a2-9e30-9bb7c44915ff (Last updated: " . date('Y-m-d H:i:s') . ")\n---\n\n";

if (file_exists($transcriptPath)) {
    $lines = file($transcriptPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $obj = json_decode($line, true);
        if ($obj && isset($obj['type']) && isset($obj['content'])) {
            if ($obj['type'] === 'USER_INPUT') {
                $content .= "\n\n**User:**\n" . $obj['content'];
            } elseif ($obj['type'] === 'PLANNER_RESPONSE' && !empty($obj['content'])) {
                $content .= "\n\n**AI:**\n" . $obj['content'];
            }
        }
    }
}

file_put_contents($backupPath, $content, FILE_APPEND);
echo "SUCCESS";
