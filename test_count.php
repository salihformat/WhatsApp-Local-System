$msg = \App\Models\Message::find(167); // wait, looking at logs: local.INFO: SendMessageJob created for message ID: 167 
// local.INFO: Message 167 sent successfully {"central_message_id":5014}
// So message 167 was sent and its central_message_id is 5014.
$count = \App\Models\Message::where('central_message_id', '86103')->count();
echo "Count local: " . $count . "\n";
$msg = \App\Models\Message::where('central_message_id', '86103')->first();
if ($msg) echo "Local ID: " . $msg->id . " Status: " . $msg->status . "\n";
