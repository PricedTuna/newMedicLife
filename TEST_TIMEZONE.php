<?php
// TEST_TIMEZONE.php - Script to test timezone offset logic

// Simulate client sending data
$clientLocalStr = "2025-11-10 17:30:00";  // What user sees on their PC
$clientOffset = 300;  // UTC-5 (e.g., EST = 300 minutes ahead of UTC)

echo "=== TIMEZONE TEST ===\n";
echo "Client sees on PC: $clientLocalStr\n";
echo "Client offset: $clientOffset minutes (negative = ahead of UTC, positive = behind)\n\n";

// Simulate server processing
$nowUtc = new DateTime('now', new DateTimeZone('UTC'));
$scheduled = DateTime::createFromFormat('Y-m-d H:i:s', $clientLocalStr, new DateTimeZone('UTC'));

echo "Now UTC: " . $nowUtc->format('Y-m-d H:i:s') . "\n";
echo "Scheduled (as parsed): " . $scheduled->format('Y-m-d H:i:s') . "\n\n";

// Calculate what "now" is in client's timezone
$nowClient = clone $nowUtc;
if ($clientOffset !== 0) {
    $sign = ($clientOffset > 0) ? '-' : '+';
    $abs = abs($clientOffset);
    $nowClient->modify("{$sign}{$abs} minutes");
}

echo "Now in client TZ: " . $nowClient->format('Y-m-d H:i:s') . "\n";

$minClient = clone $nowClient;
$minClient->modify('+10 minutes');
echo "Minimum allowed (now + 10min): " . $minClient->format('Y-m-d H:i:s') . "\n\n";

if ($scheduled >= $minClient) {
    echo "✓ VALIDATION PASSED: scheduled time is acceptable\n";
} else {
    echo "✗ VALIDATION FAILED: scheduled time is in the past\n";
}

// Show the difference
$diff = $scheduled->diff($minClient);
echo "Difference: " . $diff->format('%h hours, %i minutes, %s seconds') . "\n";

?>
