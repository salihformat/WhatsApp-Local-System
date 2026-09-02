<?php

return [
    'toast_success' => 'Success',
    'toast_error' => 'An error occurred',

    // Dashboard
    'dashboard_title' => 'WhatsApp Local Agent Dashboard',
    'services_running' => 'Services running',
    'services_stopped' => 'Services stopped',
    'dashboard_subtitle' => 'Full control over sending, active folder monitoring, queue processing, and one-click retry for stuck messages.',
    'start_services_hidden' => 'Start Services (hidden)',
    'stop_services' => 'Stop Services',
    'scan_folder_now' => 'Scan Folder Now',
    'retry_failed' => 'Retry Failed',
    'start_queue' => 'Start Queue',
    'restart_queue' => 'Restart Queue',
    'check_server_connection' => 'Check Server Connection',
    'pending_approval_title' => 'You have requests awaiting approval',
    'print_requests_count' => ':count print request(s)',
    'send_review_count' => ':count file(s) awaiting send review',
    'review_print_requests' => 'Review Print Requests',
    'review_send_requests' => 'Review Send Requests',

    'stat_total' => 'Total',
    'stat_pending' => 'Pending',
    'stat_processing' => 'Processing',
    'stat_sent' => 'Sent',
    'stat_received' => 'Received',
    'stat_delivered' => 'Delivered',
    'stat_read' => 'Read',
    'stat_failed' => 'Failed',

    'folder_status_title' => 'Monitor Folder Status',
    'folder_connected' => 'Connected & active',
    'folder_disconnected' => 'Disconnected',
    'active_path' => 'Active path:',
    'server_connected' => 'Connected to server',
    'server_disconnected' => 'Disconnected from server',
    'folder_pending_scan' => 'Awaiting scan',
    'folder_archived' => 'Archived',
    'folder_failed_files' => 'Bad files',
    'folder_not_writable' => 'The folder is not writable — archiving files may fail.',
    'delivery_trend_title' => 'Delivery Rate (Last 7 Days)',

    'recent_activity_title' => 'Recent Activity & Send Logs',
    'view_all_messages' => 'View All Messages',
    'col_message_id' => 'Message ID',
    'col_recipient' => 'Recipient',
    'col_message_content' => 'Message Content',
    'col_message_type' => 'Message Type',
    'col_attached_file' => 'Attached File',
    'col_status' => 'Status',
    'col_created_at' => 'Created At',
    'col_actions' => 'Actions',
    'type_media' => 'Media & File',
    'type_text' => 'Text Message',
    'no_active_messages' => 'No active messages yet. Drop a file in the monitor folder to get started automatically!',
    'retry_send' => 'Retry Sending',
    'view_details' => 'View Details',

    // Message statuses
    'status_read' => 'Read',
    'status_delivered' => 'Delivered',
    'status_sent' => 'Sent',
    'status_received' => 'Received',
    'status_processing' => 'Processing',
    'status_pending' => 'Pending',
    'status_cancelled' => 'Cancelled',
    'status_failed' => 'Failed',

    // Print jobs
    'print_jobs_title' => 'Print Jobs',
    'print_status_pending' => 'Pending',
    'print_status_awaiting_approval' => 'Awaiting Approval',
    'print_status_printing' => 'Printing',
    'print_status_completed' => 'Completed',
    'print_status_failed' => 'Failed',
    'print_status_rejected' => 'Rejected',
    'col_file' => 'File Name',
    'col_printer' => 'Printer',
    'col_source' => 'Source',
    'col_attempts' => 'Attempts',
    'col_pages' => 'Pages',
    'col_arrived_at' => 'Arrived At',
    'col_completed_at' => 'Completed At',
    'col_duration' => 'Duration',
    'approve' => 'Approve',
    'reject' => 'Reject',
    'approve_all' => 'Approve All',
    'reject_all' => 'Reject All',
    'retry' => 'Retry',
    'no_print_jobs' => 'No print jobs',
    'search_and_filter' => 'Search & Filter',
    'search_by_phone' => 'Search by phone number...',
    'search_by_filename' => 'Search by file name...',
    'all_printers' => 'All',
    'export' => 'Export Excel',
    'export_csv_hint' => 'Export current results to an Excel (CSV) file',
    'reset' => 'Reset',

    // Printers
    'printers_title' => 'Printer Management',
    'add_printer_title' => 'Add New Printer',
    'printer_display_name' => 'Display Name',
    'printer_windows_name' => 'Windows Printer Name',
    'printer_type' => 'Type',
    'printer_mode' => 'Print Mode',
    'printer_mode_auto' => 'Automatic (prints instantly)',
    'printer_mode_approval' => 'Requires Approval',
    'printer_default' => 'Default Printer',
    'printer_active' => 'Active',
    'printer_inactive' => 'Enabled',
    'printer_add_button' => 'Add',
    'col_windows_name' => 'Windows Name',
    'col_type' => 'Type',
    'col_mode' => 'Print Mode',
    'col_default' => 'Default',
    'col_customer_ack' => 'Confirm Print to Customer',
    'col_backup' => 'Backup',
    'col_health_status' => 'Printer Status',
    'printer_healthy' => 'Healthy',
    'printer_unhealthy' => 'Offline',
    'printer_unverified' => 'Unverified',
    'checked_minutes_ago' => ':count minutes ago',
    'check_now' => 'Check Now',
    'manage_routing_rules' => 'Manage Routing Rules',
    'view_print_log' => 'Print Job Log',
    'printer_display_placeholder' => 'Reception Printer',
    'printer_type_document' => 'Documents (PDF)',
    'printer_type_thermal' => 'Thermal / Labels (coming soon)',
    'printer_type_document_short' => 'Documents',
    'printer_type_thermal_short' => 'Thermal',
    'printer_default_hint' => 'This printer is used automatically for any printable file that doesn\'t match a routing rule — only one printer can be default at a time.',
    'col_backup_hint' => 'If this printer becomes unhealthy (per the last periodic check), new print jobs are automatically redirected to this backup printer.',
    'col_job_count' => 'Print Jobs',
    'col_pages_printed' => 'Pages Printed',
    'col_pages_printed_hint' => 'Total pages actually printed — a rough estimate for ink/paper planning.',
    'printer_mode_hint' => 'Automatic: prints instantly. Requires approval: held until approved from the dashboard or a WhatsApp reply.',
    'printer_mode_auto_short' => 'Automatic',
    'printer_disabled' => 'Disabled',
    'supports_status_check_hint' => 'Only enable after manually confirming this printer actually reports paper/ink outages via Windows.',
    'status_check_verified' => 'Verified ✓',
    'not_checked_yet' => 'Not checked yet',
    'printer_status_healthy' => 'Healthy',
    'printer_status_error' => 'Has an issue',
    'printer_status_unknown' => 'Unknown',
    'no_auto_failover' => 'No auto-failover',
    'toggle_active' => 'Enable/Disable',
    'confirm_delete_printer' => 'Are you sure you want to delete this printer? Any pending print jobs for it will be cleared.',
    'delete_printer' => 'Delete Printer',
    'no_printers_yet' => 'No printers added yet',
    'printing_disabled_notice' => 'Smart Printing is currently disabled (PRINTING_ENABLED=false in .env). You can set up printers and rules now, but no files will actually print until it\'s enabled.',

    // System health
    'system_health_title' => 'System Health Dashboard',
    'central_connection' => 'Central System Connection',
    'connection_disconnected' => 'Disconnected',
    'connection_connected' => 'Connected',
    'failed_last_hour' => 'Failed in Last Hour',
    'out_of_total_failed' => 'out of :count total failed',
    'stuck_messages' => 'Stuck Messages (+10 min)',
    'out_of_total_pending' => 'out of :count pending',
    'queue_backlog' => 'Queue Backlog',
    'jobs_not_processed_yet' => 'jobs not processed yet',
    'manage_failed_jobs' => 'Manage Technically Failed Jobs',
    'scan_all' => 'Scan All',
    'restart' => 'Restart',
    'last_scan' => 'Last scan',
    'minutes_ago' => ':count minutes ago',
    'recent_errors_title' => 'Recent Technical Errors (from this system\'s logs)',
    'print_health_title' => 'Print Health & Manual Review',
    'send_awaiting_review' => 'Sends Awaiting Manual Review',
    'review_now' => 'Review Now',
    'print_failed_today' => 'Print Failures Today',
    'print_awaiting_approval' => 'Prints Awaiting Approval',
    'printers_with_issues' => 'Printers With Issues',
    'manage_printers' => 'Manage Printers',
    'trend_last_checks' => 'Last 7 Checks Trend',
    'col_time' => 'Time',
    'col_central' => 'Central',
    'no_health_data_yet' => 'No health data yet — checks start automatically every 10 minutes.',
    'job_count' => ':count job(s)',
    'restart_queue_hint' => 'Restart the queue worker to process jobs',
    'confirm_clear_queue' => 'Warning: this permanently clears every pending job in the queue. Are you sure?',
    'clear_all_hint' => 'Permanently clear all backlogged jobs',
    'no_recent_errors' => 'No recent errors recorded.',
    'now_active' => 'Now active',
    'no_active_printers' => 'No active printers.',
    'no_history_yet' => 'No history yet',

    // Audit log
    'audit_log_title' => 'Audit Log',
    'filter' => 'Filter',
    'all_types' => 'All',
    'all_users' => 'All',
    'search_in_description' => 'Search in description...',
    'from_date' => 'From',
    'to_date' => 'To',
    'col_when' => 'When',
    'col_user' => 'User',
    'col_activity_type' => 'Type',
    'col_event' => 'Event',
    'col_description' => 'Description',
    'col_details' => 'Details',
    'view_details_link' => 'View',
    'system_actor' => 'System',
    'event_created' => 'Created',
    'event_updated' => 'Updated',
    'event_deleted' => 'Deleted',
    'event_login' => 'Login',
    'event_logout' => 'Logout',
    'log_name_default' => 'General',
    'log_name_print_monitor' => 'Print Monitor',
    'log_name_auth' => 'Authentication',
    'log_name_system' => 'System',
    'log_name_users' => 'Users',
    'log_name_messages' => 'Messages',
    'log_name_contacts' => 'Contacts',
    'log_name_print_rules' => 'Print Rules',
    'log_name_printers' => 'Printers',
    'log_name_services' => 'Services',
    'log_name_settings' => 'Settings',
    'no_audit_logs_yet' => 'No audit logs yet',

    // PrintMonitor folder tracking
    'print_monitor_title' => 'Monitor Folder Tracking (PrintMonitor)',
    'approve_all_pending' => 'Approve All Pending Files',
    'auto_send_disabled_notice' => 'Auto-send is enabled for this folder — files are sent as soon as a phone number is extracted with enough confidence. To require approval for every file before sending, set MONITOR_FOLDER_REQUIRE_APPROVAL=true in .env.',
    'folder_pending' => 'Pending (not processed yet)',
    'folder_review' => 'Awaiting Review',
    'folder_processing' => 'Processing Now',
    'folder_sent' => 'Sent Successfully',
    'folder_failed' => 'Failed',
    'col_file_name' => 'File Name',
    'col_size' => 'Size',
    'col_modified' => 'Last Modified',
    'col_phone' => 'Phone Number',
    'col_search_details' => 'Details',
    'col_failure_reason' => 'Failure Reason',
    'col_action' => 'Action',
    'no_files' => 'No files',
    'view_details_short' => 'View Details',
    'review_reason_notice' => 'These files either had their phone number extracted from a low-confidence source (no explicit label), require blanket approval, or no number could be extracted automatically — enter the number manually above then click "Send". Check "Details" to see the reason before approving or rejecting.',
    'pending_send' => 'Sending...',

    'confirm_approve_all_pending' => 'Are you sure you want to approve all files currently pending review?',
    'no_pending_review_files' => 'No files awaiting review right now',
    'folder_missing_notice' => 'The folder :path doesn\'t exist yet — it will be created automatically the first time monitor:folder runs.',
    'approval_required_notice' => '⏸️ "Approval before sending" is enabled for this folder — no file will be sent over WhatsApp automatically; an approval request reaches the admin\'s number and shows up here under "Awaiting Review" below. Approve with the button below, or reply on WhatsApp with "approve send <message id>" / "reject send <message id>" (or "send me the file send <message id>" to preview it first).',
    'last_100_only' => 'last 100 only',
    'no_phone_found' => 'No phone number found in the filename or its content',
    'stale_file_notice' => 'A stale copy of this file — the latest send attempt with the same name later succeeded (current status: :status). This file can be safely deleted.',
    'hide' => 'Hide',
    'message_record_not_found' => 'No matching message record found',
    'confirm_send_to_number' => 'This will actually send the file to this number. Continue?',
    'manual_phone_entry_hint' => 'The system couldn\'t extract a phone number automatically — enter it here',
    'send' => 'Send',
    'confirm_reject_file' => 'This will reject the file and move it to the failed folder without sending. Continue?',
    'confirm_send_to' => 'This will actually send the file to :phone. Continue?',
    'approve_and_send' => 'Approve & Send',
    'extraction_method' => 'Extraction method',
    'learned_trust_notice' => 'Manual review skipped automatically — this number is trusted based on prior approvals of the same number from the same source.',
    'rtl_corrected_notice' => 'Arabic letter-order reversal was corrected in the text before matching (a known issue in some PDF text extraction).',
    'pdf_ocr_notice' => 'The file\'s text layer was corrupted or missing — the first page was converted to an image and read via OCR to get this result.',
    'matched_label' => 'Matched label',
    'extracted_file_number' => 'Extracted file number',
    'matching_contact_found' => 'A matching contact was found',
    'no_matching_contact' => 'No contact found with this number',
    'excluded_candidates' => 'Numbers ignored during the search',
    'matched_word' => 'matched the word',
    'but_excluded_because' => 'but was excluded due to the nearby word',
    'nearby' => '',
    'confirm_default' => 'OK',
    'are_you_sure' => 'Are you sure?',
    'cancel' => 'Cancel',

    // Phone extraction reasons (PrintMonitorController::traceSourceLabel)
    'trace_filename' => 'Phone number extracted from the filename',
    'trace_label' => 'Phone number extracted from the file\'s content (labeled)',
    'trace_file_number' => 'A file number was found inside the document and looked up in contacts',
    'trace_file_number_verified' => 'No precise label found, so any number near "no"/"number" was searched for and verified directly against contacts',
    'trace_ocr_missing' => 'Optical character recognition (OCR) failed — the engine isn\'t installed',
    'trace_ocr_error' => 'Optical character recognition (OCR) failed for this file',
    'trace_empty_image_text' => 'The scanned image/page has no readable text',
    'trace_unlabeled_fallback' => 'No label found — the first number resembling a Saudi mobile number in the content was used',
    'trace_corrupted_fallback' => 'The text layer appears corrupted (Arabic encoding issue) — only an unlabeled number was searched for',
    'trace_env_fallback' => 'The fallback phone number from settings was used (MONITOR_FALLBACK_PHONE)',
    'trace_parse_error' => 'Failed to read the file\'s content',
    'trace_empty_text' => 'The file has no readable text layer',
    'trace_no_match_in_content' => 'No valid number or label was found in the content',
    'trace_none' => 'No extraction was attempted (no filename or content match)',
    'trace_unknown' => 'Unknown',

    // Server connection status (DashboardController)
    'checking_connection' => 'Checking...',
    'server_connected_ok' => 'Connected to server successfully (authentication OK)',
    'server_connected_with_error' => 'Connected but there\'s an error: :detail',
    'check_token_and_company' => 'Check the token and company ID',
    'server_unreachable' => 'Can\'t reach the server (check the URL or that the server is running)',
    'connection_error' => 'Connection error: :detail',
    'server_url_not_set' => 'Server URL not set (CENTRAL_API_URL)',
    'whatsapp_not_checked' => 'Not checked yet (central server disconnected)',
    'whatsapp_connected' => 'WhatsApp Connected',
    'whatsapp_disconnected' => 'WhatsApp Disconnected',
    'whatsapp_check_failed' => 'Could not verify WhatsApp status',
    'check_whatsapp_connection' => 'Check WhatsApp Connection',
    'central_blocking_error_title' => 'A blocking error is preventing all messages from being sent from the central system',
    'read_more' => 'Read more',

    // Print routing rules
    'print_rules_title' => 'Print Routing Rules',
    'print_rules_need_printer' => 'You need to :link at least one before creating routing rules.',
    'add_a_printer' => 'add a printer',
    'add_routing_rule' => 'Add Routing Rule',
    'rule_name' => 'Rule Name',
    'rule_name_placeholder' => 'e.g. Accounting Invoices',
    'match_type' => 'Match Type',
    'match_phone_number' => 'Specific phone number',
    'match_phone_prefix' => 'Phone number prefix',
    'match_keyword' => 'Keyword',
    'match_file_type' => 'File extension',
    'match_value' => 'Value',
    'match_value_hint' => 'You can enter multiple comma-separated values, for any match type',
    'match_value_hint_short' => 'You can enter multiple comma-separated values',
    'match_value_placeholder' => 'e.g. print,طباعة,اطبع — or numbers: 966501111111,966502222222',
    'priority' => 'Priority',
    'priority_hint' => 'Lower is checked first',
    'add_rule' => 'Add Rule',
    'condition' => 'Condition',
    'condition_phone_eq' => 'number =',
    'condition_prefix_eq' => 'prefix =',
    'condition_contains_word' => 'contains word',
    'condition_ext_eq' => 'extension =',
    'action_type' => 'Action',
    'action_print_and_send' => 'Print + Send via WhatsApp',
    'action_print_only' => 'Print only, don\'t send (default)',
    'action_send_only' => 'Send only, don\'t print',
    'action_save_only' => 'Save only (no print, no send)',
    'action_hold_for_approval' => 'Hold for manual approval',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'confirm_delete_rule' => 'Are you sure you want to delete this rule?',

    // Conversations index
    'conv_search_placeholder' => 'Search by phone number or name...',
    'conv_filter' => 'Filter',
    'conv_reset' => 'Reset',
    'conv_media' => 'Media',
    'conv_no_messages' => 'No messages',
    'conv_open_tooltip' => 'Open conversation',
    'conv_close_tooltip' => 'Close conversation',
    'conv_confirm_close' => 'Are you sure you want to close this conversation?',

    // Conversation show
    'conv_end' => 'End conversation',
    'conv_reopen' => 'Reopen conversation',
    'conv_closed_notice' => 'This conversation is closed. You cannot send new messages.',
    'conv_load_older' => 'Load older messages',
    'conv_edit_tooltip' => 'Edit',
    'conv_attachment' => 'Attachment',
    'conv_no_previous_messages' => 'No previous messages. Start the conversation now!',
    'conv_attach_file' => 'Attach file',
    'conv_quick_replies' => 'Quick replies',
    'conv_no_quick_replies' => 'No quick replies',
    'conv_type_message' => 'Type a message...',
    'conv_closed_notice_full' => 'This conversation is closed. You cannot send new messages unless you reopen it.',
    'conv_assign_title' => 'Assign conversation',
    'conv_unassigned' => 'Unassigned',
    'conv_save' => 'Save',
    'conv_internal_notes' => 'Internal notes',
    'conv_activity_log' => 'Activity log',
    'conv_confirm_delete_note' => 'Are you sure you want to delete this note?',
    'conv_no_internal_notes' => 'No internal notes',
    'conv_note_placeholder' => 'Write a note visible to staff only...',
    'conv_add_note' => 'Add note',
    'conv_no_activity_log' => 'No activity log',
    'conv_js_done_title' => 'Done',
    'conv_js_ok' => 'OK',
    'conv_js_alert_title' => 'Notice',
    'conv_js_error_title' => 'Error',
    'conv_file_too_large' => 'File is too large (max size 10 MB)',
    'conv_err_generic' => 'An error occurred while connecting to the server.',
    'conv_err_413' => 'File is too large and exceeds the allowed limit.',
    'conv_err_419' => 'Your session has expired, please refresh the page.',
    'conv_err_422' => 'The submitted data is invalid.',
    'conv_send_failed_title' => 'Send failed',
    'conv_unknown_error' => 'Unknown error',
    'conv_connection_lost' => 'Internet connection lost or server unavailable.',
    'conv_new_attachment_received' => 'New attachment received',
    'conv_new_message_from' => 'New message from',
    'conv_close_conversation_title' => 'Close conversation',
    'conv_yes_close' => 'Yes, close it',
    'conv_edit_message_title' => 'Edit message',
    'conv_write_message_here' => 'Write your message here',
    'conv_save_edits' => 'Save changes',
    'conv_message_empty' => 'Message cannot be empty',
    'conv_update_failed_prefix' => 'Update failed',
    'conv_edited_success' => 'Edited!',
    'conv_delete_message_title' => 'Delete message',
    'conv_confirm_delete_message' => 'Are you sure you want to delete this message? This cannot be undone.',
    'conv_yes_delete' => 'Yes, delete it',
    'conv_delete_failed_prefix' => 'Delete failed',
    'conv_message_deleted_marker' => '🚫 This message was deleted',
    'conv_deleted_success' => 'Deleted!',

    // Failed jobs
    'fj_total_records' => 'Total records',
    'fj_all_queues' => 'All queues',
    'fj_clear' => 'Clear',
    'fj_export_excel' => 'Export Excel',
    'fj_restart_queue_worker_title' => 'Restart the queue worker (the service that processes jobs)',
    'fj_restart_services' => 'Restart services',
    'fj_confirm_retry_all' => 'Are you sure you want to retry all failed jobs?',
    'fj_retry_all' => 'Retry all',
    'fj_confirm_flush' => 'Warning: this will permanently clear all failed jobs from the log. Are you sure?',
    'fj_flush_all' => 'Flush all',
    'fj_col_id' => 'ID',
    'fj_col_queue_type' => 'Queue / Type',
    'fj_col_failed_at' => 'Failed at',
    'fj_col_error' => 'Technical error',
    'fj_unknown' => 'Unknown',
    'fj_queue_label' => 'Queue',
    'fj_retry' => 'Retry',
    'fj_confirm_forget' => 'Are you sure you want to clear this job?',
    'fj_no_failed_jobs' => 'No failed jobs in the log',
    'fj_back_to_system_health' => 'Back to System Health',

    // Users
    'users_list_title' => 'User List',
    'users_export_title' => 'Export results to Excel (CSV)',
    'users_add_user' => 'Add User',
    'users_col_name' => 'Name',
    'users_col_email' => 'Email',
    'users_col_role' => 'Role',
    'users_auto_distribution_title' => 'Does this user receive new conversations via automatic distribution? See conversation distribution settings',
    'users_col_auto_distribution' => 'Auto distribution',
    'users_col_last_login' => 'Last Login',
    'users_col_created_at' => 'Created at',
    'users_role_admin' => 'Admin',
    'users_role_supervisor' => 'Supervisor',
    'users_role_agent' => 'Customer service agent',
    'users_available_title' => 'Available to automatically receive new conversations',
    'users_available' => 'Available',
    'users_unavailable' => 'Unavailable',
    'users_confirm_delete' => 'Are you sure you want to delete this user?',
    'users_phone_hint' => 'If entered, this user receives an actual WhatsApp notification (in addition to the notification bell) when a conversation is assigned to them.',
    'users_col_account_status' => 'Account Status',
    'users_account_status_title' => 'Enable or disable user account — disabled users cannot log in',
    'users_status_active' => 'Active',
    'users_status_inactive' => 'Suspended',
    'users_confirm_deactivate' => 'Are you sure you want to deactivate this account? The user will not be able to log in until you re-enable it.',
    'users_confirm_activate' => 'Are you sure you want to activate this account? The user will be able to log in again.',
    'users_deactivate_title' => 'Click to deactivate this account',
    'users_activate_title' => 'Click to activate this account',
    'users_account_deactivated_hint' => 'Account suspended',
    'users_own_account_hint' => 'You cannot deactivate your own account',
    'edit_rule' => 'Edit Rule',
    'save_changes' => 'Save Changes',
    'no_rules_yet' => 'No rules yet — any incoming PDF will only be sent to the default printer (if one is set)',

    // Settings page
    'settings_title' => 'System Settings',
    'save_settings' => 'Save Settings',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',
    'comma_separated' => 'comma-separated',

    'settings_system_info' => 'System & Device Info',
    'settings_local_system_name' => 'Local System Name (LOCAL_SYSTEM_NAME)',
    'settings_local_system_name_placeholder' => 'Riyadh Branch',
    'settings_local_system_name_help' => 'A distinct name for this local system (shown in alerts and reports).',
    'settings_device_name' => 'Device Name (DEVICE_NAME)',
    'settings_location' => 'Location (LOCATION)',
    'settings_plan_type' => 'Plan Type (PLAN_TYPE)',

    'settings_central_api' => 'Central API',
    'settings_company_id' => 'Company ID (CENTRAL_API_COMPANY_ID)',
    'settings_api_token' => 'Connection Token (CENTRAL_API_TOKEN)',
    'settings_api_token_help' => 'Used by this system as a Bearer token when calling the central system\'s API (sending messages, syncing status). Must match the "secret token" shown on the company page in the central system.',
    'settings_webhook_token' => 'Incoming Webhook Verification Token (CENTRAL_WEBHOOK_TOKEN)',
    'settings_webhook_token_placeholder' => 'Leave empty to temporarily fall back to CENTRAL_API_TOKEN (not recommended)',
    'settings_webhook_token_help' => '<strong>Best practice:</strong> a token completely independent from the one above — this system uses it to verify that an incoming webhook request genuinely came from the central system, not the same token used for outbound calls. It must match the "token" value for this specific system\'s webhook endpoint on the "Connected External Systems" page in the central system (not the company\'s general secret token). Only leave it empty for temporary compatibility with the old setup.',
    'settings_retry_attempts' => 'Reconnection Attempts (CENTRAL_API_RETRY_ATTEMPTS)',
    'settings_retry_delay' => 'Reconnection Delay in Seconds (CENTRAL_API_RETRY_DELAY)',

    'settings_local_retry' => 'Local Retry',
    'settings_max_retry_attempts' => 'Max Resend Attempts (MAX_RETRY_ATTEMPTS)',
    'settings_retry_delay_minutes' => 'Resend Delay in Minutes (RETRY_DELAY_MINUTES)',

    'settings_alerts_monitoring' => 'Alerts & Monitoring',
    'settings_admin_phone' => 'Admin Phone Number (PRINTER_ALERT_PHONE)',
    'settings_admin_phone_help' => 'The admin phone number(s), comma-separated, that receive all alerts: printer outages, approval requests, and system health alerts. Leave empty to disable alerts.',
    'settings_default_message_text' => 'Default Message Text (MONITORING_MESSAGE_TEXT)',
    'settings_require_approval' => 'Require Approval Before Sending (MONITOR_FOLDER_REQUIRE_APPROVAL)',
    'settings_require_approval_off' => 'Disabled (instant automatic sending — the usual mode)',
    'settings_require_approval_on' => 'Enabled (every file held pending manual approval)',
    'settings_require_approval_help' => 'When enabled, every file arriving through the monitor folder is held pending explicit approval before being sent via WhatsApp.',

    'settings_smart_printing' => 'Smart Printing',
    'settings_printing_enabled' => 'Enable Smart Printing (PRINTING_ENABLED)',
    'settings_page_size' => 'Paper Size (PRINT_IMAGE_PAGE_SIZE)',
    'settings_page_size_help' => 'Must match the paper actually loaded in the printer.',
    'settings_dpi' => 'Print Resolution DPI (PRINT_IMAGE_DPI)',
    'settings_dpi_help' => '200 is a balanced value between clarity and file size.',
    'settings_reminder_after' => 'Remind About Approval Request After (minutes)',
    'settings_reminder_after_help' => 'How many minutes an unanswered approval request sits before an automatic reminder is sent to the admin.',
    'settings_reminder_repeat' => 'Repeat Reminder Every (minutes)',
    'settings_reminder_repeat_help' => 'Set to 0 to disable automatic reminders entirely.',
    'settings_printable_extensions' => 'Printable Extensions (PRINTABLE_EXTENSIONS)',

    'settings_printing_notifications' => 'Smart Printing Status Notifications',
    'settings_ack_on_receipt' => 'Instant Reply on Receiving the Request (PRINTING_REPLY_ACK_ON_RECEIPT)',
    'settings_ack_on_receipt_on' => 'Enabled ("📥 Your request has been received and is being processed" as soon as it\'s logged)',
    'settings_ack_on_receipt_help' => 'A separate message from the final result reply — reassures the customer their file arrived correctly.',
    'settings_reply_to_sender' => 'Reply to Whoever Requested Printing (PRINTING_REPLY_STATUS_TO_SENDER)',
    'settings_reply_to_sender_on' => 'Enabled (the customer gets an automatic message on success/failure of their file\'s printing)',
    'settings_reply_to_sender_help' => 'Not sent on every failed attempt — only on final success or failure after all attempts are exhausted.',
    'settings_notify_owner' => 'Technical Alert to the Business Owner on Failure (PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE)',
    'settings_notify_owner_on' => 'Enabled (the full technical error reaches the admin\'s number)',

    'settings_file_settings' => 'File Settings',
    'settings_storage_path' => 'Storage Path (FILE_STORAGE_PATH)',
    'settings_max_size' => 'Max Size in Megabytes (FILE_MAX_SIZE_MB)',
    'settings_auto_delete_days' => 'Auto-Delete After (days) (FILE_AUTO_DELETE_DAYS)',
    'settings_backup_retention' => 'Backup Retention (days) (BACKUP_RETENTION_DAYS)',
    'settings_backup_retention_help' => 'Number of days to keep automatic backups before deleting them.',
    'settings_allowed_types' => 'Allowed Types (FILE_ALLOWED_TYPES)',

    'settings_extraction_title' => 'Extracting Phone Numbers From File Content (PDF/DOCX)',
    'settings_extraction_intro' => 'These settings are used when the filename doesn\'t contain a phone number, so the system searches inside the file\'s own content.',
    'settings_country_code' => 'Default Country Code (DEFAULT_COUNTRY_CODE)',
    'settings_smart_extraction' => 'for smart extraction',
    'settings_country_code_placeholder' => 'e.g. 966 or 20',
    'settings_country_code_help' => 'Added automatically if a number is extracted without a country code or starting with a leading zero (the system drops the zero and adds this code).',
    'settings_extraction_method' => 'Number Extraction Method (PRINT_EXTRACTION_METHOD)',
    'settings_extraction_ocr' => 'Automatically read the number from the file\'s content (smart default)',
    'settings_extraction_popup' => 'Manual entry from the website when automatic extraction fails',
    'settings_extraction_filename' => 'Extract from filename only (doesn\'t search inside the file)',
    'settings_extraction_method_help' => 'In all three modes, the number is first attempted from the filename, then from its content (except in "filename only" mode). The difference only shows when all extraction attempts fail: "smart default"/"filename only" move the file straight to the "failed" folder, while "manual entry from the website" holds it in the "Awaiting Review" tab on the send-tracking page (<a href="' . url('/print-monitor') . '" class="text-indigo-600 hover:underline">/print-monitor</a>) so you enter the number manually there and it gets sent. There is no actual system popup — that could never work in a scheduled task with no interactive session (Session 0), so it was replaced with this practical web-based path.',
    'settings_phone_labels' => 'Phone Number Search Labels (PHONE_EXTRACTION_LABELS)',
    'settings_phone_labels_help' => 'If a match is found near one of these words, the number right after it is treated as the customer\'s phone number.',
    'settings_exclude_context' => 'Exclusion Words (PHONE_EXTRACTION_EXCLUDE_CONTEXT)',
    'settings_exclude_context_help' => 'If one of these words appears right before a phone number (like "store phone"), that number is ignored because it belongs to the issuer, not the customer.',
    'settings_file_number_labels' => 'File Number Search Labels (FILE_NUMBER_LABELS)',
    'settings_file_number_labels_help' => 'When a number is found next to one of these words, the system looks up a contact with the same file number and sends to their phone number.',
    'settings_match_mode' => 'Match Mode (PHONE_MATCH_MODE)',
    'settings_match_partial' => 'Partial (the word as part of a longer string)',
    'settings_match_exact' => 'Exact (the word must stand entirely on its own)',
    'settings_match_mode_help' => '"Partial" matches "mobile" even inside "mymobile"; "exact" requires the word to be fully separate.',
    'settings_unlabeled_fallback' => 'Allow Extraction Without a Label (ENABLE_UNLABELED_PHONE_FALLBACK)',
    'settings_unlabeled_fallback_on' => 'Enabled (searches for any number resembling a Saudi mobile number, with no label, as a last resort)',
    'settings_unlabeled_fallback_off' => 'Disabled (if no explicit label is found, the file moves to the "failed" folder)',
    'settings_review_sources' => 'Sources Requiring Manual Review Before Sending (PHONE_REVIEW_REQUIRED_SOURCES)',
    'settings_review_sources_help' => 'Instead of sending automatically right away, any file whose number was extracted from one of these (low-confidence) sources is held in the "Awaiting Review" tab on the send-tracking page until you manually approve it.',
    'settings_review_sources_values' => 'Possible values',
    'settings_review_sources_empty' => 'Leave empty to always send automatically (no review).',

    'settings_duplicate_learning_title' => 'Duplicate Detection & Learning From Manual Corrections',
    'settings_duplicate_detection' => 'Duplicate Detection (DUPLICATE_DETECTION_ENABLED)',
    'settings_duplicate_detection_help' => 'Holds for manual review any file whose content matches a file already sent to the same number recently, instead of auto-sending it a second time.',
    'settings_duplicate_window' => 'Duplicate Detection Window in Minutes (DUPLICATE_DETECTION_WINDOW_MINUTES)',
    'settings_learned_trust_threshold' => 'Learned Trust Threshold (LEARNED_TRUST_THRESHOLD)',
    'settings_learned_trust_threshold_help' => 'Number of times the same number from the same low-confidence source must be manually approved before review is skipped automatically going forward. A single rejection drops trust immediately. 0 disables the feature.',

    'settings_distribution_title' => 'New Conversation Distribution',
    'settings_distribution_mode' => 'Assignment Mode (CONVERSATION_DISTRIBUTION_MODE)',
    'settings_distribution_manual' => 'Manual (no automatic assignment — assignment only from the conversations page)',
    'settings_distribution_specific' => 'Automatic, to specific users',
    'settings_distribution_all' => 'Automatic, to every available agent (role = agent)',
    'settings_distribution_help' => 'With automatic assignment (any mode other than "manual"), every new conversation goes to whoever among the eligible group currently has the <strong>fewest open conversations</strong> — genuine load balancing, not a blind rotation that ignores one agent\'s pile-up. Automation rules (assignment by number/keyword from the "Automation" page) still run independently of this setting and apply afterward, so they can override automatic assignment for specific cases.',
    'settings_distribution_users' => 'Users Included in Distribution (CONVERSATION_DISTRIBUTION_USER_IDS)',
    'settings_currently_unavailable' => 'currently unavailable',
    'settings_no_users_yet' => 'No users yet.',
    'settings_distribution_users_help' => 'A user marked "currently unavailable" (see the user management page) is automatically excluded from distribution even if still selected here — useful for temporarily excluding them (e.g. on leave) without editing this list.',

    'settings_backlog_threshold' => 'Queue Backlog Alert Threshold (HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD)',
    'settings_backlog_threshold_help' => 'Above how many backlogged jobs in the processing queue an instant WhatsApp alert is sent to the admin.',
    'settings_alert_cooldown' => 'Alert Cooldown Period (minutes) (HEALTH_ALERT_COOLDOWN_MINUTES)',
    'settings_alert_cooldown_help' => 'After a system health alert is sent, how many minutes to wait before allowing it to repeat. Set to 0 to disable alerts entirely.',

    // Compose new message
    'compose_title' => 'Send New Message',
    'compose_phone_intl' => 'Phone Number (international)',
    'compose_phone_placeholder' => 'Search by name or number... or enter a new number',
    'compose_searching' => 'Searching...',
    'compose_no_results' => 'No matching results',
    'remove' => 'Remove',
    'compose_phone_help' => 'Enter the country code followed by the phone number (without + or 00), or search by customer name',
    'compose_message_text' => 'Message Text',
    'compose_message_placeholder' => 'Write your message here...',
    'compose_attachments' => 'Attachments (multiple files)',
    'compose_choose_files' => 'Choose Files',
    'compose_or_drag' => 'or drag and drop',
    'compose_file_types_hint' => 'PNG, JPG, PDF, DOC, XLS up to 10MB per file',
    'compose_selected_files' => 'Selected files:',
    'compose_delete_all' => 'Remove All',
    'compose_tips_title' => 'Important Tips',
    'compose_tip_1' => 'Make sure the international number entered is correct',
    'compose_tip_2' => 'Use numbers in international format (e.g. 966501234567)',
    'compose_tip_3' => 'You can select several files at once — they\'ll be sent with a random 1–10 second gap between each',
    'compose_tip_4' => 'Maximum file size is 10MB',
    'compose_send' => 'Send Message',

    // Performance reports
    'reports_title' => 'Customer Service Performance Reports',
    'reports_total_conversations' => 'Total Conversations',
    'reports_open_conversations' => 'Open Conversations',
    'reports_closed_conversations' => 'Closed Conversations',
    'reports_this_month' => 'Conversations This Month',
    'reports_agent_performance' => 'Agent Performance (Closed Conversations)',
    'reports_agent_name' => 'Agent Name',
    'reports_closed_count' => 'Conversations Closed',
    'reports_percentage' => 'Percentage',
    'reports_no_data' => 'No data available',
    'reports_message_distribution' => 'Message Distribution',
    'reports_incoming' => 'Incoming Messages (from customers)',
    'reports_outgoing' => 'Outgoing Messages (from the system)',
    'reports_total_messages' => 'Total Messages in the System',

    // PDF tools
    'pdf_merge_title' => 'Merge PDF Files',
    'pdf_merge_help' => 'Choose two or more files to merge into one, in the same order. You can add files from different locations by clicking "Add Files" multiple times.',
    'pdf_add_files' => 'Add Files',
    'pdf_selected_files' => 'Selected files (will be merged in this order):',
    'pdf_merge_button' => 'Merge & Download',
    'pdf_merge_min_files' => 'You must select at least two files to merge',
    'pdf_split_title' => 'Extract Pages From PDF',
    'pdf_split_help' => 'Choose a file and the page range to extract as a separate file.',
    'pdf_from_page' => 'From page',
    'pdf_to_page' => 'To page',
    'pdf_split_button' => 'Extract & Download',
    'pdf_compress_title' => 'Compress Image',
    'pdf_compress_help' => 'To shrink a large image before sending it over WhatsApp (JPG/PNG).',
    'pdf_quality' => 'Quality (10-95)',
    'pdf_compress_button' => 'Compress & Download',

    // General automation engine
    'automation_title' => 'General Automation Engine',
    'automation_intro' => 'When a new message arrives, each active rule\'s condition is checked in order (lowest priority number first), and only the first matching rule\'s action runs.',
    'automation_add_rule' => 'Add Automation Rule',
    'automation_rule_name_placeholder' => 'Route VIP Complaints',
    'automation_condition_type' => 'Condition Type',
    'automation_match_keyword_multi' => 'Keyword (comma-separate for more than one)',
    'automation_condition_value' => 'Condition Value',
    'automation_condition_value_placeholder' => 'complaint,issue,inquiry',
    'automation_action_type' => 'Action Type',
    'automation_action_assign' => 'Assign the conversation to an agent',
    'automation_action_note' => 'Add an internal note',
    'automation_action_reply' => 'Instant automatic reply',
    'automation_action_value' => 'Action Value',
    'automation_choose_agent' => '— Choose an agent (only for the "assign to agent" action) —',
    'automation_action_value_placeholder' => 'The note text or automatic reply, or the agent ID above',
    'automation_action_hint' => 'For the "assign to agent" action: pick the agent from the list (auto-fills the field with their ID). For every other action: type the text directly.',
    'automation_assigned_to_agent' => 'Assigned to agent #',
    'automation_note_label' => 'Note:',
    'automation_reply_label' => 'Auto-reply:',
    'automation_no_rules' => 'No automation rules yet',

    // Docs page (auto-generated section blocks)
    'docs_page_title' => 'Local WhatsApp System User Guide',
    'docs_s1' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">1. How to run it on a local machine</h2>
                            <p class="mb-4">To run the system on your own machine for the first time, follow these steps:</p>
                            <ul class="list-disc list-inside space-y-2 mr-4">
                                <li>Make sure a local server environment is installed (XAMPP or Laragon).</li>
                                <li>Create a new database named <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">whatsapp_local</code>.</li>
                                <li>Copy the file <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">.env.example</code> to <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">.env</code> and update the database connection details.</li>
                                <li>Open a terminal in the project folder and run the following commands:
                                    <pre class="bg-gray-900 text-gray-100 p-4 rounded mt-2 dir-ltr">composer install
php artisan migrate --seed
php artisan serve --port=8001</pre>
                                </li>
                                <li>You can now access the site via: <a href="http://127.0.0.1:8001" class="text-blue-500 underline">http://127.0.0.1:8001</a></li>
                            </ul>
                            <div class="mt-4 bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border-r-4 border-indigo-400">
                                <p class="text-sm">These steps are enough for testing and development only. For actual daily use on the business's machine (running automatically after every boot with no manual intervention, on a fixed port, with the queue worker and scheduler), see section <strong>8. Full automatic startup</strong> below — this is the actually recommended method, not the manual commands above.</p>
                            </div>
EOT,
    'docs_s2' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">2. Connecting to the Central System</h2>
                            <p class="mb-4">For the local system to be able to send messages through the main server, the connection settings must be configured in the <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">.env</code> file:</p>

                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg space-y-4">
                                <div>
                                    <h3 class="font-bold text-[#f53003]">CENTRAL_API_URL</h3>
                                    <p class="text-sm">The API URL of the central system. Example: <code class="bg-white dark:bg-gray-800 px-1">http://your-central-domain.com/api</code></p>
                                </div>

                                <div>
                                    <h3 class="font-bold text-[#f53003]">CENTRAL_API_COMPANY_ID (Company ID)</h3>
                                    <p class="text-sm">This number is obtained from the central system's control panel (Companies section). It represents the unique identity of your branch or company.</p>
                                </div>

                                <div>
                                    <h3 class="font-bold text-[#f53003]">CENTRAL_API_TOKEN (security token)</h3>
                                    <p class="text-sm">The security token generated by the central system for each company. It's placed here to ensure the connection is encrypted and authorized.</p>
                                </div>

                                <div>
                                    <h3 class="font-bold text-[#f53003]">API_ENCRYPTION_KEY</h3>
                                    <p class="text-sm">An additional encryption key for sensitive data. You can generate it using the following command in the terminal:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-2 rounded mt-1 text-xs dir-ltr">php -r "echo bin2hex(random_bytes(32));"</pre>
                                </div>

                                <div>
                                    <h3 class="font-bold text-[#f53003]">MONITOR_FOLDER_PATH (monitor folder)</h3>
                                    <p class="text-sm">The full path of the folder the system will monitor to pick up files and send them automatically. Example: <code class="bg-white dark:bg-gray-800 px-1">C:/PrintMonitor</code> — see section 6 below for a full explanation of how to use this folder.</p>
                                </div>
                            </div>
EOT,
    'docs_s3' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">3. Overview of the system's pages</h2>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Dashboard</h3>
                                    <p>The main hub of the system, showing quick stats on the number of sent, failed, and pending messages. It also has buttons for manual control such as "Check folder" and "Retry failed messages" (admins only).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Conversations / Messages</h3>
                                    <p>Shows all incoming and outgoing conversations and messages with each message's status (sent, failed, delivered, read), with the ability to resend a failed message or run bulk actions.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">PDF Tools</h3>
                                    <p>Merge multiple PDF files into one, split a PDF file, and compress images — available to all registered users, not an admin-only feature.</p>
                                </div>

                                <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border-r-4 border-indigo-400">
                                    <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 mb-1">The following pages are for Admins only, and appear under the "Admin" menu in the top bar:</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">User Management (Users)</h3>
                                    <p>Add new users or edit existing users' data and permissions (admin/supervisor/agent).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Settings</h3>
                                    <p>Configure the connection settings with the central system, file and retry settings, as well as <strong>phone number extraction settings from file contents</strong> (phone number search keywords, exclusion keywords, matching pattern, sources requiring manual review) and <strong>smart printing status notification settings</strong> — without needing to manually edit the <code>.env</code> file.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Performance Reports (Reports)</h3>
                                    <p>Statistical reports on sending performance and conversation activity.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Printers and Routing Rules (Printers)</h3>
                                    <p>Add/edit printers connected to the machine, set a default printer, check each printer's status instantly (paper/ink/connection), and mark any printer as "trusted" to send the customer a genuine print confirmation (see section 4 below for details).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Print Monitor</h3>
                                    <p>A live view of the monitor folder (C:\PrintMonitor): files pending, awaiting manual review, being processed, sent successfully, or failed — with the extracted phone number, the failure reason, and full details of the number-extraction mechanism for each file (which keyword matched, and which numbers were excluded and why).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Print Job Log (Print Jobs)</h3>
                                    <p>A complete log of every print job: request arrival time, print completion time, duration, number of attempts, and the failure reason if any.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Automation (Automation Rules)</h3>
                                    <p>Automatic rules to assign an agent, add an internal note, or send an instant auto-reply when an incoming message matches certain conditions — with built-in protection against auto-reply loops.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">System Health</h3>
                                    <p>A chart tracking pending/failed messages and queue backlog over time, and the connection status with the central system.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Audit Log</h3>
                                    <p>A complete log of every administrative change (who changed what and when) — logins, settings changes, user management, printers, and more.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">Profile</h3>
                                    <p>A page for each user to change their personal data such as name, email, and password.</p>
                                </div>
                            </div>
EOT,
    'docs_s4' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">4. Smart Printing in detail</h2>
                            <p class="mb-4">When a PDF file, image (jpg, png, gif, bmp, tiff), or office document (doc, docx, xls, xlsx, ppt, pptx) arrives via WhatsApp (or is placed in the monitor folder) containing a print keyword (like "اطبع", "print"), the system automatically runs through the following steps:</p>
                            <p class="text-xs text-gray-500 mb-4">Note: Word/Excel/PowerPoint files are automatically converted to PDF via LibreOffice (see section 5) before printing — with no extra action required from you. Supported extensions are customizable via <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">PRINTABLE_EXTENSIONS</code> in <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">.env</code>.</p>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2">1. Extracting the phone number / file number</h3>
                                    <p class="text-sm">First from the file name, then from the file content (PDF/DOCX) via customizable keywords from the settings page. If all normal methods fail due to a corrupted text layer (a common encoding issue in some scanned PDF files), the system finally tries reading the file as an image via Tesseract OCR (see section 5). If the extraction came from a low-confidence source (with no explicit labeling), the file is held in the "Awaiting review" tab on the Print Monitor page instead of being sent automatically.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2">2. Instant receipt reply</h3>
                                    <p class="text-sm">The customer immediately receives: "📥 Your print request has been received and is being processed now..." — before the print actually happens, to reassure them their file arrived correctly.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-purple-500">
                                    <h3 class="text-lg font-bold mb-2">3. Actual printing + verification</h3>
                                    <p class="text-sm">The file is sent to the matching printer via SumatraPDF (see section 5). If the printer is marked as "trusted" on the Printers page (supports genuine fault reporting), the system checks its status before and after printing (out of paper/ink/disconnected). Not every printer model supports this — some drivers (like older USB printers) never report these states to Windows at all, so every new printer's default status is "unverified" until you manually check and enable it.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-orange-500">
                                    <h3 class="text-lg font-bold mb-2">4. Final result reply</h3>
                                    <p class="text-sm">
                                        <strong>Trusted printer:</strong> The customer receives an accurate confirmation — "✅ Printed successfully" or "❌ Print failed, reason: ..." (a simplified reason, without technical details).<br>
                                        <strong>Unverified printer:</strong> The customer receives no additional success/failure confirmation (to avoid a false confirmation) — the receipt message from step 2 is all they get. A failure caused by a genuine software error (downloading the file, the printing tool crashing, a timeout) is always reported regardless of this setting.
                                    </p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2">5. Business owner alert</h3>
                                    <p class="text-sm">When a print finally fails (after exhausting all retries), a separate technical alert is sent to the number <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> with the full real error — unlike the simplified message the customer receives.</p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                                ⚠️ Important operational note: any code change (such as message wording or check logic) requires <strong>restarting the Queue Worker</strong> from the dashboard to take effect — the worker keeps the code in memory from its last start and does not automatically read modified files.
                            </p>

                            <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <h3 class="font-bold mb-3 text-[#f53003]">Printing images — special preparation before printing</h3>
                                <p class="text-sm mb-3">WhatsApp images (JPG/PNG...) always arrive <strong>with no DPI resolution data</strong> embedded in the file. If sent to the printer as-is, SumatraPDF computes the print "page" size directly from the image's pixel dimensions, producing a non-standard (custom) size that matches no paper tray — either nothing prints at all despite the command succeeding programmatically, or an "out of paper" message appears despite paper actually being loaded. So the system automatically places every image inside a full standard page (white background, the image scaled down if needed and centered on the page while preserving its proportions) before sending it to print.</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><code class="bg-white dark:bg-gray-800 px-1">PRINT_IMAGE_PAGE_SIZE</code> (in <code class="bg-white dark:bg-gray-800 px-1">.env</code>, default <code class="bg-white dark:bg-gray-800 px-1">a4</code>): the page size (<code class="bg-white dark:bg-gray-800 px-1">a4</code> or <code class="bg-white dark:bg-gray-800 px-1">letter</code>) — <strong>must match the actual paper size loaded in the printer</strong>, otherwise the "out of paper" message will reappear.</li>
                                    <li><code class="bg-white dark:bg-gray-800 px-1">PRINT_IMAGE_DPI</code> (default <code class="bg-white dark:bg-gray-800 px-1">200</code>): the resolution forced on the image before printing. A balanced value between clarity and file size — usually no need to change it.</li>
                                </ul>
                                <p class="text-xs text-gray-500 mt-2">The prepared local copies are stored in <code class="bg-white dark:bg-gray-800 px-1">storage/app/private/print_jobs</code>, and are automatically deleted after the same <code class="bg-white dark:bg-gray-800 px-1">FILE_AUTO_DELETE_DAYS</code> duration used to clean up the monitor folder (see section 6) — via the same scheduled <code class="bg-white dark:bg-gray-800 px-1">files:clean-old</code> command, with no separate setting needed.</p>
                            </div>

                            <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <h3 class="font-bold mb-3 text-[#f53003]">How does the system pick the right printer? (routing rules)</h3>
                                <p class="text-sm mb-3">From the "Printers and Routing Rules" page, you can add rules that determine which printer is used based on: a specific phone number, a number prefix, a keyword in the message text, or a file extension. Order: rules are checked by priority (lowest first), and the first enabled matching rule wins. <strong>More than one value can be placed in the same rule, separated by a comma</strong> — for any match type, not just keywords. Example of a phone-number rule: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">966501111111,966502222222,966503333333</code> matches any of these three numbers.</p>

                                <div class="bg-red-50 dark:bg-red-900/20 border-r-4 border-red-400 p-4 rounded-lg mb-3">
                                    <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">⚠️ Very important warning about the "Default" printer</p>
                                    <p class="text-sm">If no rule matches the incoming message, the system uses the <strong>printer marked as default</strong> (if any) to print the file — <strong>completely regardless of whether the message contains a print keyword or not</strong>. In other words: any default printer prints every incoming PDF file automatically, even if the customer never typed "print" at all. Because of this: do not set a default printer unless you actually want every incoming PDF printed without exception. If you rely on keywords to control what gets printed, leave all printers "non-default" and rely only on the keyword/number rules.</p>
                                </div>

                                <h4 class="font-bold text-sm mb-2">Scenario: the local system running across several branches</h4>
                                <p class="text-sm mb-2">If several branches share the same central company WhatsApp number (the current default situation, with no automatic routing to the correct branch from the central system), then every incoming message reaches all registered local systems at once. To avoid printing the same file at every branch together:</p>
                                <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                    <li>Make the print keyword <strong>different and unique per branch</strong> (not a shared generic word like "اطبع" alone) — e.g. Riyadh branch: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">طباعة_رياض,اطبع_رياض</code>, and Jeddah branch: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">طباعة_جدة,اطبع_جدة</code>.</li>
                                    <li><strong>Do not set a default printer in any branch</strong> (see the warning above) — otherwise every branch would print any file unconditionally, regardless of the keyword.</li>
                                    <li>Inform each branch's customers of their correct keyword (a sign, an automatic welcome message, etc.) since the customer is the one who actually determines the branch through the word they type.</li>
                                    <li>A safer alternative/complement for known customers: add a <code class="bg-white dark:bg-gray-800 px-1">specific phone number</code> rule with those customers' numbers (comma-separated as above) for each branch, so their messages are routed to their branch's printer automatically without needing to type any keyword.</li>
                                </ol>
                                <p class="text-xs text-gray-500 mt-2">The most complete architectural solution (requires a change in the central system, not implemented currently) is linking each branch to its own independent WhatsApp number. The keyword approach above is a practical alternative that needs no changes and works with the current settings directly.</p>
                            </div>
EOT,
    'docs_s5' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">5. Required external programs</h2>
                            <p class="mb-4">The system does not print or read scanned images/documents by itself — it relies on free external programs that must be installed on the same machine:</p>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">SumatraPDF — for printing PDF files automatically and silently</h3>
                                    <p class="text-sm mb-2">A free, lightweight program used to send PDF files directly to the printer without opening any window (silent printing). Without it, the entire Smart Printing feature will not work.</p>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>Download the Portable version from the official site: <a href="https://www.sumatrapdfreader.org" class="text-blue-500 underline" target="_blank">sumatrapdfreader.org</a></li>
                                        <li>Place the file <code class="bg-white dark:bg-gray-800 px-1">SumatraPDF.exe</code> at the path: <code class="bg-white dark:bg-gray-800 px-1">C:/SumatraPDF/SumatraPDF.exe</code> (or any other path you choose).</li>
                                        <li>Make sure the file path exactly matches the value of <code class="bg-white dark:bg-gray-800 px-1">SUMATRA_PDF_PATH</code> in the <code class="bg-white dark:bg-gray-800 px-1">.env</code> file (it can also be edited later from the settings page if added there).</li>
                                        <li>Make sure <code class="bg-white dark:bg-gray-800 px-1">PRINTING_ENABLED=true</code> is set in <code class="bg-white dark:bg-gray-800 px-1">.env</code> to fully enable the feature.</li>
                                    </ol>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-indigo-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">LibreOffice — for printing Word/Excel/PowerPoint files</h3>
                                    <p class="text-sm mb-2">SumatraPDF does not understand office formats directly, so LibreOffice is used in "headless" mode (silent, no window opens) to convert the file to PDF first, which is then printed via the usual PDF path. Optional — without it, PDF and image printing keep working normally, and only office-file printing specifically fails.</p>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>Download and install the full version (not Portable) from the official site: <a href="https://www.libreoffice.org/download/download/" class="text-blue-500 underline" target="_blank">libreoffice.org</a></li>
                                        <li>The default path after installation: <code class="bg-white dark:bg-gray-800 px-1">C:/Program Files/LibreOffice/program/soffice.exe</code> — matches the default value of <code class="bg-white dark:bg-gray-800 px-1">LIBREOFFICE_PATH</code> in <code class="bg-white dark:bg-gray-800 px-1">.env</code>, so nothing needs to change if you installed it at the default path.</li>
                                        <li>If you chose a different path, update it in <code class="bg-white dark:bg-gray-800 px-1">LIBREOFFICE_PATH</code> in the <code class="bg-white dark:bg-gray-800 px-1">.env</code> file.</li>
                                    </ol>
                                    <p class="text-xs text-gray-500 mt-2">Performance note: the first conversion after every Queue Worker restart may take a minute or more (a one-time internal warm-up), and subsequent conversions are usually much faster. The timeout is adjustable via <code class="bg-white dark:bg-gray-800 px-1">OFFICE_CONVERSION_TIMEOUT_SECONDS</code> (default 120 seconds).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-teal-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">Tesseract OCR — for reading text from images and scanned files</h3>
                                    <p class="text-sm mb-2">Used as a last resort to extract a phone number when normal methods fail (e.g.: a PDF file that's actually a scanned image with no real text layer, or a jpg/png image sent directly). Optional — the system works without it, but these specific cases will fail to extract a number without it installed.</p>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>Download the Windows installer from: <a href="https://github.com/UB-Mannheim/tesseract/wiki" class="text-blue-500 underline" target="_blank">UB-Mannheim/tesseract (Windows builds)</a></li>
                                        <li>Install it at the default path: <code class="bg-white dark:bg-gray-800 px-1">C:/Program Files/Tesseract-OCR</code></li>
                                        <li>Add the following line to the <code class="bg-white dark:bg-gray-800 px-1">.env</code> file:
                                            <pre class="bg-gray-900 text-gray-100 p-2 rounded mt-1 text-xs dir-ltr">TESSERACT_BIN_PATH="C:/Program Files/Tesseract-OCR/tesseract.exe"</pre>
                                        </li>
                                        <li>During installation make sure to select the Arabic language pack in addition to English, to support reading Arabic documents.</li>
                                    </ol>
                                    <p class="text-xs text-gray-500 mt-2">To verify a correct install: open Command Prompt and run <code class="bg-white dark:bg-gray-800 px-1">"C:\Program Files\Tesseract-OCR\tesseract.exe" --version</code> — the version number should appear with no errors.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-purple-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">Ghostscript — to convert a PDF page to an image before reading it with OCR</h3>
                                    <p class="text-sm mb-2">A complement to Tesseract OCR above: when a PDF's text layer is completely corrupted (a common font-encoding fault in some scanned files) or missing entirely, Ghostscript is used to convert the file's first page into a PNG image, which is then read by Tesseract as a last resort. Fully optional, just like Tesseract — without it the system works normally, but this specific case (a PDF with a corrupted text layer) will not be resolved automatically.</p>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>Download the Windows (64-bit) installer from the official site: <a href="https://ghostscript.com/releases/gsdnld.html" class="text-blue-500 underline" target="_blank">ghostscript.com/releases</a> or from the <a href="https://github.com/ArtifexSoftware/ghostpdl-downloads/releases" class="text-blue-500 underline" target="_blank">official GitHub</a>.</li>
                                        <li>Install it with default settings (usual path: <code class="bg-white dark:bg-gray-800 px-1">C:/Program Files/gs/gsX.XX.X/bin/gswin64c.exe</code>, where X.XX.X is the version number).</li>
                                        <li>Add the following line to the <code class="bg-white dark:bg-gray-800 px-1">.env</code> file with the actual path after your install:
                                            <pre class="bg-gray-900 text-gray-100 p-2 rounded mt-1 text-xs dir-ltr">GHOSTSCRIPT_BIN_PATH="C:/Program Files/gs/gs10.07.1/bin/gswin64c.exe"</pre>
                                        </li>
                                    </ol>
                                    <p class="text-xs text-gray-500 mt-2">To verify a correct install: run <code class="bg-white dark:bg-gray-800 px-1">"C:\Program Files\gs\gsX.XX.X\bin\gswin64c.exe" --version</code> in Command Prompt — the version number should appear with no errors.</p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                                ⚠️ After installing or changing the path of any of these programs in <code>.env</code>, you must <strong>restart the Queue Worker</strong> from the dashboard for it to read the new setting.
                            </p>
EOT,
    'docs_s6' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">6. How to use the monitor folder (PrintMonitor)</h2>
                            <p class="mb-4">This folder (by default <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">C:\PrintMonitor</code>, set via <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">MONITOR_FOLDER_PATH</code>) is the second way to send a file via WhatsApp — instead of sending it through an actual WhatsApp conversation, you place the file directly in this folder on the computer and the system sends it automatically.</p>

                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg mb-4">
                                <h3 class="font-bold mb-2 text-[#f53003]">Daily usage steps</h3>
                                <ol class="list-decimal list-inside text-sm space-y-2 mr-4">
                                    <li>Open the folder <code class="bg-white dark:bg-gray-800 px-1">C:\PrintMonitor</code> on the computer (not inside the website).</li>
                                    <li>Place or copy the invoice/document file (PDF, DOCX, JPG, PNG...) directly in the **root** of this folder (not inside any of its subfolders).</li>
                                    <li>
                                        To embed the phone number directly, name the file so it contains the phone number (9 to 15 digits) in its name, e.g.:
                                        <code class="bg-white dark:bg-gray-800 px-1">0512345678_invoice.pdf</code> or <code class="bg-white dark:bg-gray-800 px-1">966512345678.pdf</code>.
                                    </li>
                                    <li>
                                        If the file name has no number, the system will try to read the number (or the file number linked to a contact) from the file's **content** itself — see section 4 above for the details of this mechanism, and the settings page to customize the search keywords used.
                                    </li>
                                    <li>Within seconds (per <code class="bg-white dark:bg-gray-800 px-1">MONITORING_INTERVAL_SECONDS</code>, by default every minute via the scheduler), the system will pick up the file automatically and send it.</li>
                                </ol>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <h3 class="font-bold mb-2 text-[#f53003]">Subfolders (created automatically — do not place files in them manually)</h3>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><code class="bg-white dark:bg-gray-800 px-1">processing/</code> — files currently being processed and sent.</li>
                                    <li><code class="bg-white dark:bg-gray-800 px-1">review/</code> — files whose number was extracted from a low-confidence source and need your manual approval from the "Print Monitor" page before they're actually sent.</li>
                                    <li><code class="bg-white dark:bg-gray-800 px-1">archive/</code> — files that were sent successfully.</li>
                                    <li><code class="bg-white dark:bg-gray-800 px-1">failed/</code> — files that failed to send (usually because a valid phone number couldn't be found) — you can review the reason from the "Print Monitor" page.</li>
                                </ul>
                                <p class="text-sm mt-3">To track each file's status live (arrived, awaiting review, succeeded, failed and why), use the <strong>Print Monitor</strong> page instead of opening these folders manually.</p>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg mt-4">
                                <h3 class="font-bold mb-2 text-[#f53003]">Automatic deletion of old files</h3>
                                <p class="text-sm mb-2">So these folders don't fill up over time, the system automatically deletes, every day, files older than a certain age from the four subfolders (<code class="bg-white dark:bg-gray-800 px-1">processing</code>, <code class="bg-white dark:bg-gray-800 px-1">review</code>, <code class="bg-white dark:bg-gray-800 px-1">archive</code>, <code class="bg-white dark:bg-gray-800 px-1">failed</code>) — but not the main pending folder (the folder root) which holds files not yet processed. <strong>The same duration also applies to the temporary local copies of print files</strong> in <code class="bg-white dark:bg-gray-800 px-1">storage/app/private/print_jobs</code> (see section 4).</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><strong>To set the duration:</strong> from the settings page → <code class="bg-white dark:bg-gray-800 px-1">Auto Delete Days (FILE_AUTO_DELETE_DAYS)</code> — any number of days you want, no file editing needed. Current default value: 3 days.</li>
                                    <li>Set the value to <code class="bg-white dark:bg-gray-800 px-1">0</code> to disable automatic deletion entirely and keep all files forever.</li>
                                    <li>Files in the <code class="bg-white dark:bg-gray-800 px-1">review</code> folder whose review deadline passes without a manual decision are also deleted, and their message status automatically changes to "failed" with the reason noted (timeout), instead of staying stuck on the Print Monitor page indefinitely.</li>
                                    <li>The command responsible for this: <code class="bg-white dark:bg-gray-800 px-1">php artisan files:clean-old</code> — runs automatically as part of the daily schedule (no need to run it manually).</li>
                                </ul>
                            </div>
EOT,
    'docs_s7' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">7. Running background processing and scheduling manually (for testing only)</h2>
                            <p class="mb-4">The following method is useful for testing or diagnosing an issue directly on screen. For actual daily use, use automatic startup (section 8) instead of these manual commands.</p>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2">First: the Queue Worker</h3>
                                    <p class="text-sm mb-2">This command is responsible for sending messages, processing printing, and syncing contacts:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan queue:work --queue=contacts-sync,webhooks,default</pre>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2">Second: the Scheduler</h3>
                                    <p class="text-sm mb-2">This command is responsible for checking the monitor folder and running periodic tasks (syncing, checking printers, checking for expired files, etc.):</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan schedule:run</pre>
                                    <p class="text-xs text-gray-500 mt-2">This command runs a single check and then stops — it needs to be repeated every minute. Alternative: <code class="bg-white dark:bg-gray-800 px-1">php artisan schedule:work</code> keeps running continuously and executes each task at its scheduled time automatically (this is the command actually used in the automatic startup setup in section 8).</p>
                                </div>
                            </div>
EOT,
    'docs_s8' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">8. Full automatic startup (ready-made setup scripts)</h2>
                            <p class="mb-4">Instead of manually running the previous commands in Terminal windows that must stay open (and stop when closed or the machine restarts), the system provides ready-made scripts in the <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">scripts/</code> folder that set up a complete automatic startup which runs on its own after every boot — the site, the database, printing, and scheduling, with no manual intervention. This is the recommended method for the actual work machine, and is the same setup needed to configure the system on a new machine.</p>

                            <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border-r-4 border-indigo-400 mb-4">
                                <p class="text-sm font-semibold mb-2">How does it work? (5 independent components, each registered differently based on its nature)</p>
                                <ul class="list-disc list-inside text-sm space-y-2 mr-4">
                                    <li>
                                        <strong>Apache (serving the site):</strong> copied from the XAMPP install into an isolated copy dedicated to this project only (<code class="bg-white dark:bg-gray-800 px-1">scripts/apache-standalone</code>), on its own port (from <code class="bg-white dark:bg-gray-800 px-1">APP_URL</code>), without any change to the original XAMPP install or any other project on the same machine — then it's registered as a Windows service (<code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalApache</code>) that runs automatically on boot, <strong>with automatic recovery configured on crash</strong> (<code class="bg-white dark:bg-gray-800 px-1" dir="ltr">sc.exe failure</code> — 3 restart attempts, every 60 seconds) — unlike the default Windows service behavior (never restarted automatically without this explicit setting).
                                    </li>
                                    <li>
                                        <strong>MySQL (database):</strong> unlike Apache, here the same MySQL install that already exists inside XAMPP is registered (with its current data as-is, no copying or changes) as a Windows service (<code class="bg-white dark:bg-gray-800 px-1">MySQL_XAMPP</code>) with the same auto-recovery setup above. This step is necessary because XAMPP <strong>does not register MySQL as an automatic service by default</strong> — without it the site fails entirely (HTTP 500 error, "connection refused") after every machine restart even though Apache itself runs successfully, because every page needs the database.
                                    </li>
                                    <li>
                                        <strong>Queue Worker (<code class="bg-white dark:bg-gray-800 px-1">queue:work</code>):</strong> registered as a Task Scheduler task named <code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalSystem-QueueWorker</code>, with two triggers: "At startup" and "At log on" (the second is more reliable for this specific task — see the warning below), with automatic restart on failure.
                                    </li>
                                    <li>
                                        <strong>Scheduler (<code class="bg-white dark:bg-gray-800 px-1">schedule:work</code>):</strong> same idea as the queue worker, as a separate task named <code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalSystem-Scheduler</code>, responsible for running all periodic tasks (syncing, checking printers, auto-deletion...) — see section 7.
                                    </li>
                                    <li>
                                        <strong>Automatic update pulling (<code class="bg-white dark:bg-gray-800 px-1">auto-update.ps1</code>):</strong> a separate task named <code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalSystem-AutoUpdate</code> running under the SYSTEM account (no interactive session needed) every 10 minutes — it checks the <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">main</code> branch on GitHub, and if a new update exists it pulls it (<code class="bg-white dark:bg-gray-800 px-1">git pull</code>) and automatically applies <code class="bg-white dark:bg-gray-800 px-1">composer install</code>, <code class="bg-white dark:bg-gray-800 px-1">npm run build</code>, and <code class="bg-white dark:bg-gray-800 px-1">migrate</code>, then restarts the queue worker to apply the new code. See the box below for full details.
                                    </li>
                                </ul>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border-r-4 border-purple-400 mb-4">
                                <h3 class="text-lg font-bold mb-2 text-[#f53003]">Automatic update pulling from GitHub</h3>
                                <p class="text-sm mb-2">Once automatic startup setup (section below) is complete, the system automatically checks the <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">main</code> branch on GitHub every 10 minutes — any update pushed (<code class="bg-white dark:bg-gray-800 px-1">push</code>) to this branch reaches this machine and is fully applied within 10 minutes at most, with no manual intervention.</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><strong>Safety:</strong> the script never touches any local uncommitted changes on the machine — if it finds the local tree isn't clean (<code class="bg-white dark:bg-gray-800 px-1" dir="ltr">git status</code> not empty), it stops immediately and logs a warning instead of ignoring or discarding the changes.</li>
                                    <li><strong>Backup before every migration:</strong> the script takes a quick database backup (<code class="bg-white dark:bg-gray-800 px-1" dir="ltr">mysqldump</code>) into <code class="bg-white dark:bg-gray-800 px-1">storage/app/private/db_backups</code> <strong>right before</strong> applying any new migration — if the backup itself fails, the script deliberately stops before migrating (instead of risking a dangerous update with no backup to roll back to). Only the last 10 backups are kept automatically.</li>
                                    <li><strong>Instant alert on failure:</strong> any failure in pulling or updating is automatically sent as a WhatsApp message to the number <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> (the same printer-health alert number), instead of the failure staying hidden in a log file nobody reviews.</li>
                                    <li><strong>The log:</strong> every update operation (successful or failed) is logged in detail in <code class="bg-white dark:bg-gray-800 px-1">storage/logs/auto-update.log</code> — check it to confirm the last update applied or to find the reason for any failure.</li>
                                    <li><strong>Important warning:</strong> since any <code class="bg-white dark:bg-gray-800 px-1">push</code> to <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">main</code> reaches the real production machine directly with no human review, <strong>never push untested code to this branch</strong> — test it on another branch first if it's experimental.</li>
                                    <li>To disable this feature: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">Unregister-ScheduledTask -TaskName "WhatsAppLocalSystem-AutoUpdate"</code> from an elevated PowerShell.</li>
                                </ul>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2">To activate on a new machine — including cloning from GitHub (one time only)</h3>
                                    <p class="text-sm mb-2">Requirements: XAMPP installed at its default path <code class="bg-white dark:bg-gray-800 px-1">C:\xampp</code> only (includes PHP, Apache, and MySQL) — <strong>no need to install Node.js or Composer manually</strong>, the following scripts install them automatically. Run the following four files from the <code class="bg-white dark:bg-gray-800 px-1">scripts</code> folder <strong>in numeric order</strong> (the same order is also documented in <code class="bg-white dark:bg-gray-800 px-1">scripts/README-Installation.txt</code>):</p>
                                    <ol class="list-decimal list-inside text-sm space-y-2 mr-4">
                                        <li>
                                            <code class="bg-white dark:bg-gray-800 px-1">01-Install-Prerequisites.bat</code> (right-click → Run as Administrator): adds PHP's path to the system, installs Node.js and Composer, plus SumatraPDF, Tesseract OCR, and LibreOffice (the last three via winget, safely skipping any already-installed program).
                                            <br><strong class="text-amber-700 dark:text-amber-400">Very important:</strong> after the success message appears, <strong>close the black CMD window</strong> before continuing — necessary for Windows to recognize the new Node and Composer paths in any window opened afterward.
                                        </li>
                                        <li>
                                            <code class="bg-white dark:bg-gray-800 px-1">02-Setup-Project.bat</code> (run normally with a double-click, no admin rights needed): prepares <code class="bg-white dark:bg-gray-800 px-1">.env</code> (copies it from <code class="bg-white dark:bg-gray-800 px-1">.env.example</code> if it doesn't exist), runs <code class="bg-white dark:bg-gray-800 px-1">composer install</code>, generates <code class="bg-white dark:bg-gray-800 px-1">APP_KEY</code>, creates the database (<code class="bg-white dark:bg-gray-800 px-1">whatsapp_local</code>) and runs <code class="bg-white dark:bg-gray-800 px-1">migrate</code>, links the public storage folder (<code class="bg-white dark:bg-gray-800 px-1">storage:link</code> — necessary for WhatsApp attachments to display correctly), then builds the frontend (<code class="bg-white dark:bg-gray-800 px-1">npm install &amp;&amp; npm run build</code>).
                                            <br><strong>Make sure the MySQL service is running</strong> (from the XAMPP control panel) before running this file, otherwise the database creation step will fail.
                                        </li>
                                        <li><code class="bg-white dark:bg-gray-800 px-1">03-Install-AutoStart.bat</code> (right-click → Run as Administrator): sets up the full automatic startup (Apache + MySQL as Windows services, and the queue worker and scheduler as scheduled tasks) — the same script described in detail in the rest of this section.</li>
                                        <li>Wait for the "Setup completed successfully!" message in the final step, then open the link shown (example: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">http://localhost:8006</code>) to confirm the site is actually working.</li>
                                    </ol>
                                    <p class="text-xs text-gray-500 mt-2"><strong>Ghostscript alone</strong> still needs to be installed manually (no reliable winget package for it; a rare case anyway — see section 5).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2">How do I confirm everything is working after a machine restart?</h3>
                                    <p class="text-sm mb-2">Open PowerShell as administrator and run the following commands — all statuses should show <code class="bg-white dark:bg-gray-800 px-1">Running</code>:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr text-xs">Get-Service WhatsAppLocalApache, MySQL_XAMPP | Select Name, Status
Get-ScheduledTask WhatsAppLocalSystem-QueueWorker, WhatsAppLocalSystem-Scheduler | Select TaskName, State</pre>
                                    <p class="text-xs text-gray-500 mt-2">If a task shows <code class="bg-white dark:bg-gray-800 px-1">Ready</code> instead of <code class="bg-white dark:bg-gray-800 px-1">Running</code>, start it manually once with <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">Start-ScheduledTask -TaskName "name"</code> — see the warning below on why this sometimes happens.</p>
                                    <p class="text-xs text-gray-500 mt-2">Note: the <code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalSystem-AutoUpdate</code> task differs from the others — it runs for a few seconds every 10 minutes then returns to the <code class="bg-white dark:bg-gray-800 px-1">Ready</code> state between each run (normal, not a fault), instead of staying continuously <code class="bg-white dark:bg-gray-800 px-1">Running</code> like the queue worker and scheduler.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2">To uninstall</h3>
                                    <p class="text-sm">Run <code class="bg-white dark:bg-gray-800 px-1">04-Uninstall-AutoStart.bat</code> from the same folder the same way — it removes the Apache service and the queue worker and scheduler tasks. <strong>It deliberately does not remove the MySQL service</strong> (it may be used by other projects on the same machine) — remove it manually from <code class="bg-white dark:bg-gray-800 px-1">services.msc</code> only if you're sure nothing else depends on it.</p>
                                </div>
                            </div>

                            <div class="mt-4 bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border-r-4 border-amber-400">
                                <p class="text-sm font-semibold mb-1">Very important warnings:</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li>After this setup, <strong>do not use</strong> the (start/stop/restart services) buttons in the system's dashboard or the XAMPP control panel — operations are now managed directly via Windows Services / Task Scheduler, and using both methods together may run duplicate workers for the same task.</li>
                                    <li>The queue worker specifically needs a <strong>user actually logged in</strong> on the machine (not just the machine powered on) — this is technically required because silent printing via SumatraPDF needs a real interactive session and doesn't work reliably under the background SYSTEM account. For an office machine that stays logged in continuously, this is not noticeable in practice.</li>
                                    <li><strong>Actually observed:</strong> the "At startup" trigger alone for the queue worker task can sometimes silently fail if it tries to run before the actual desktop login completes (Windows doesn't automatically retry in this case despite the restart setting, because the task never "started" from its point of view). So an "At log on" trigger for the same user is also added as a safety layer — if you still notice the task stayed stopped after a rare boot, start it manually once as described in the paragraph above.</li>
                                    <li>Any change to code files (PHP) after this setup <strong>requires manually restarting the queue worker</strong> from the dashboard (the "Restart Queue" button) for the changes to take effect — the registered worker only keeps the code copy from its last start.</li>
                                </ul>
                            </div>
EOT,
    'docs_s9' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">9. Running on hosting servers</h2>
                            <p class="mb-4">When deploying the project on a hosting provider (such as a VPS or Shared Hosting) instead of a local Windows machine, the following must be set up:</p>

                            <h3 class="text-lg font-bold mb-2">1. Setting up the Cron Job</h3>
                            <p class="mb-2">Add the following line in the Cron Job settings of the hosting control panel (runs every minute):</p>
                            <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1</pre>

                            <h3 class="text-lg font-bold mb-2 mt-4">2. Running the worker continuously</h3>
                            <p class="mb-2">It's best to use **Supervisor** on Linux servers to ensure the worker stays running at all times. A simple example config:</p>
                            <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr text-xs">
[program:whatsapp-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --queue=contacts-sync,webhooks,default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log</pre>
                            <p class="text-sm mt-3 text-gray-500">Note: the Smart Printing feature (SumatraPDF) is Windows-only and does not work on Linux hosting — hosting is usually used for the central system, not the local system which needs an actual printer connected to the same machine.</p>
EOT,
    'docs_s10' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">10. Manually syncing contacts (Contacts Sync)</h2>
                            <p class="mb-4">Contacts are synced automatically via scheduled tasks, but you can also trigger it manually at any time:</p>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-purple-500">
                                    <h3 class="text-lg font-bold mb-2">1. Sync via the queue (recommended):</h3>
                                    <p class="text-sm mb-2">This command queues the sync job to be processed in the background (requires the Queue Worker to be running):</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan contacts:sync</pre>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2">2. Instant sync (no queue):</h3>
                                    <p class="text-sm mb-2">To run the sync immediately and see the results directly on screen (useful for finding the cause of an issue or error if any):</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan contacts:sync --now</pre>
                                </div>
                            </div>
EOT,
    'docs_s11' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">11. Important notes</h2>
                            <ul class="list-disc list-inside space-y-2 mr-4 text-gray-700 dark:text-gray-300">
                                <li>The system relies on files placed in the <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">C:\PrintMonitor</code> folder to process and send them via WhatsApp — see section 6 for the full explanation.</li>
                                <li>Make sure the "Scheduler" service and the Queue Worker keep running in the background continuously to ensure sending and printing keep working — the safest way is automatic startup from section 8 instead of manual startup.</li>
                                <li>You can monitor the connection status with the central service from the dashboard and the "System Health" page.</li>
                                <li><strong>After any change to code files</strong>, restart the Queue Worker from the dashboard — it doesn't read the changes automatically while running.</li>
                                <li>SumatraPDF is required for the Smart Printing feature to work, and Tesseract OCR is optional and used only as a last resort for reading scanned files or images — see section 5.</li>
                            </ul>
EOT,
    'docs_s12' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">12. Connecting the system to the internet for free (Cloudflare)</h2>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-bold mb-2 text-[#f53003]">Method one: quick test connection (no account)</h3>
                                <p class="mb-2">Run the following file located in the scripts folder:</p>
                                <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr mb-4">scripts\05-Start-Quick-Tunnel.bat</pre>
                                <p class="text-sm mb-6">It will give you a random link you can use immediately to access the system from anywhere in the world (this link changes when the computer restarts).</p>

                                <h3 class="text-lg font-bold mb-2 text-[#f53003]">Method two: permanent connection (requires a free account)</h3>
                                <p class="mb-2">To get a fixed, professional link that always runs in the background as a Windows service:</p>
                                <ul class="list-decimal list-inside space-y-2 mr-4 text-gray-700 dark:text-gray-300">
                                    <li>Create a free account on the <strong>Cloudflare Zero Trust</strong> platform.</li>
                                    <li>Go to <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">Networks</code> &gt; <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">Tunnels</code> and click <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">Create a tunnel</code> (choose the Cloudflared type).</li>
                                    <li>On the installation page (Windows), you'll find a command containing your token.</li>
                                    <li>Open Command Prompt (CMD) <strong>as administrator</strong> in the <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">scripts</code> folder, and run the command:</li>
                                </ul>
                                <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr mt-2 mb-2">cloudflared.exe service install YOUR_TOKEN_HERE</pre>
                                <p class="text-sm">Then from the Cloudflare site, point the tunnel (Service) to <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">http://localhost:8006</code> and the link will work permanently and reliably!</p>
                            </div>
EOT,
    'docs_s13' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">13. Recent updates: print and send approval + a separate print folder per printer</h2>
                            <p class="mb-4">Three related additions, whose goal is to give the admin full control over any printing/sending that happens automatically without human review, while keeping the current behavior (fully automatic) as the default with no behavior change unless you enable it yourself.</p>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-purple-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">1. Print mode per printer: automatic or requires approval</h3>
                                    <p class="text-sm mb-2">From the "Printers" page, each printer has a new "Print mode" column with two values:</p>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li><strong>Automatic</strong> (default, same old behavior): any matching file is printed immediately with no intervention.</li>
                                        <li><strong>Requires approval</strong>: any print request matching this printer (whether via a WhatsApp attachment or the printer's own independent folder, item 3 below) is held in "Awaiting approval" status on the "Print Jobs" page, and is only printed after explicit approval.</li>
                                    </ul>
                                    <p class="text-sm mt-2">Useful for printers that consume expensive supplies (color ink, special paper) or have sensitive access you want to manually review every request for.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-orange-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">2. Approving print requests</h3>
                                    <p class="text-sm mb-2">When a print request is held awaiting approval, an instant WhatsApp alert is sent to the number <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> (the same printer-health alert number) with the request details (job number, source, file name, printer). Approval happens one of two ways:</p>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li>The "Approve" / "Reject" buttons on the "Print Jobs" page next to any job in "Awaiting approval" status.</li>
                                        <li>Replying to the alert message via WhatsApp from the same <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> number with one of the following texts (the system does not support interactive buttons currently, just plain text replies):
                                            <ul class="list-disc list-inside mr-6 mt-1 space-y-1">
                                                <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">وافق طباعة &lt;job number&gt;</code> — prints immediately.</li>
                                                <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">رفض طباعة &lt;job number&gt;</code> — cancelled without printing.</li>
                                                <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">ارسل لي الملف طباعة &lt;job number&gt;</code> — sends you the file itself via WhatsApp to preview before deciding (also works after printing completes, not only before approval).</li>
                                                <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">وافق الكل طباعة</code> — approves all currently pending print jobs at once (a matching button also exists on the Print Jobs page).</li>
                                            </ul>
                                        </li>
                                    </ul>
                                    <p class="text-xs text-gray-500 mt-2"><code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> supports more than one admin number separated by commas (example: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">966501111111,966502222222</code>) — every number receives all alerts, and any of them can reply with approval commands.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">3. A separate print folder per printer (no relation to WhatsApp at all)</h3>
                                    <p class="text-sm mb-2">In addition to the main monitor folder (section 6, which extracts a phone number and sends the file via WhatsApp), there are now subfolders dedicated purely to direct local printing — with no need for a phone number or WhatsApp sending at all:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr mb-2">C:\PrintMonitor\print\&lt;printer name&gt;\</pre>
                                    <p class="text-sm mb-2">These folders are created automatically for every enabled printer on the first run of the <code class="bg-white dark:bg-gray-800 px-1">monitor:folder</code> command (you'll also find them shown under each printer's name on the "Printers" page). Any file placed directly in a specific printer's folder is printed automatically or held awaiting approval, depending on that printer's "print mode" (item 1 above) — with the same approval mechanism and text commands as item 2. Each printer has 4 subfolders managed automatically: the root (place the file here), <code class="bg-white dark:bg-gray-800 px-1">processing</code> (being processed/pending), <code class="bg-white dark:bg-gray-800 px-1">archive</code> (printed successfully), <code class="bg-white dark:bg-gray-800 px-1">failed</code> (failed or rejected).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">4. Approval for WhatsApp sending from the main monitor folder</h3>
                                    <p class="text-sm mb-2">Completely independent of the print approval above, you can also require approval before sending any file from the main monitor folder (<code class="bg-white dark:bg-gray-800 px-1">C:\PrintMonitor</code>) via WhatsApp — not just the low-confidence cases (section 4, item 1) but every file. Enabled by setting <code class="bg-white dark:bg-gray-800 px-1">MONITOR_FOLDER_REQUIRE_APPROVAL=true</code> in <code class="bg-white dark:bg-gray-800 px-1">.env</code> (default <code class="bg-white dark:bg-gray-800 px-1">false</code> = automatic sending as usual).</p>
                                    <p class="text-sm mb-2">Once enabled, a WhatsApp alert is sent to the same <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> number, and approval is either via a button on the "Print Monitor" page or a text reply similar to item 2 but with the word "ارسال" instead of "طباعة":</p>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">وافق ارسال &lt;message number&gt;</code></li>
                                        <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">رفض ارسال &lt;message number&gt;</code></li>
                                        <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">ارسل لي الملف ارسال &lt;message number&gt;</code> — to preview it first</li>
                                    </ul>
                                    <p class="text-xs text-gray-500 mt-2">The type word ("طباعة" or "ارسال") is always mandatory in all the commands above — because print job numbering and monitor-folder message numbering are completely independent (both start from 1), so without specifying the type it's hard to tell which table the number refers to.</p>
                                </div>
                            </div>
EOT,
    'docs_s14' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">14. Distributing conversations to agents + assignment alerts</h2>
                            <p class="mb-4">A dynamic setting (from the "Settings" page) that controls how an agent is assigned to every new incoming WhatsApp conversation — with no automatic assignment by default (same old behavior) unless you enable it yourself.</p>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-indigo-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">1. Assignment mode (CONVERSATION_DISTRIBUTION_MODE)</h3>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li><strong>Manual</strong> (default): no automatic assignment — the agent is assigned manually from the conversation page, or automatically only via a specific automation rule (the "Automation" page) if one exists.</li>
                                        <li><strong>Automatic for specific users</strong>: fair distribution among a list of agents you choose from the settings page (checkbox selection).</li>
                                        <li><strong>Automatic for all agents</strong>: the same fair distribution, but among all users with the "agent" role who are available for assignment (no list to choose).</li>
                                    </ul>
                                    <p class="text-sm mt-2">"Fair" here means: every new conversation goes to whoever currently has the <strong>fewest open conversations</strong> among the eligible group — a real load balance, not a blind round-robin that ignores a particular agent's backlog (on leave/slow). Assignment only happens for genuinely new conversations — an ongoing conversation is never redistributed, so the same agent keeps following the same customer. Automation rules (assignment by number/keyword) still work after this distribution and can override it for specific cases.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-teal-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">2. Including/excluding an agent from automatic distribution</h3>
                                    <p class="text-sm">From the "Users" page, an "Auto distribution" column for each agent — a quick one-click toggle to temporarily exclude an agent (on leave/busy) from the distribution cycle without removing them from the list chosen in Settings or changing their role.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-pink-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">3. Alerting an agent when a conversation is assigned to them</h3>
                                    <p class="text-sm mb-2">When a conversation is assigned to an agent (automatically or manually), they receive two alerts (including the customer's name/number and their last message):</p>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li><strong>The notification bell</strong> in the top bar — an unread-notifications counter, with a dropdown list, polled every 20 seconds (no page refresh needed). Browser (Desktop) notifications can also be enabled from the same menu.</li>
                                        <li><strong>An actual WhatsApp message</strong> — only arrives if the agent has a WhatsApp number registered (a new optional field in the edit-user page). Without a number, the bell notification alone remains sufficient.</li>
                                    </ul>
                                </div>
                            </div>
EOT,
    'docs_s15' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">15. Self-maintenance of the system (reminders, health alerts, backups)</h2>
                            <p class="mb-4">A set of automatically scheduled commands (see <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">routes/console.php</code>) aimed at reducing the need for daily manual monitoring of the system.</p>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-amber-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">Automatic reminder for pending approval requests</h3>
                                    <p class="text-sm">The <code class="bg-white dark:bg-gray-800 px-1">printing:send-approval-reminders</code> command (every 10 minutes) sends a WhatsApp reminder for any approval request (print or send) still pending after <code class="bg-white dark:bg-gray-800 px-1">PRINTING_APPROVAL_REMINDER_AFTER_MINUTES</code> minutes (default 20), then repeats every <code class="bg-white dark:bg-gray-800 px-1">PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES</code> minutes (default 30) as long as it stays pending. Set the second value to 0 to disable the reminder entirely.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">WhatsApp alert when queue processing stalls</h3>
                                    <p class="text-sm">The existing <code class="bg-white dark:bg-gray-800 px-1">monitor:system</code> command (every 10 minutes) now also sends an instant WhatsApp alert to the number <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> when there's a large backlog in the processing queue (above <code class="bg-white dark:bg-gray-800 px-1">HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD</code>, default 50) or messages pending for more than 10 minutes — a common indicator that the Queue Worker has silently stopped. Only one alert is sent, then a cooldown period (<code class="bg-white dark:bg-gray-800 px-1">HEALTH_ALERT_COOLDOWN_MINUTES</code>, default 60 minutes) before it repeats, with a separate recovery notice once the issue clears.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-emerald-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">Automatic daily database backup</h3>
                                    <p class="text-sm">The <code class="bg-white dark:bg-gray-800 px-1">backup:database</code> command (daily at 3 AM) creates a compressed (gzip) mysqldump backup in <code class="bg-white dark:bg-gray-800 px-1">storage/app/backups</code>, and automatically deletes any backup older than <code class="bg-white dark:bg-gray-800 px-1">BACKUP_RETENTION_DAYS</code> days (default 14). Requires setting <code class="bg-white dark:bg-gray-800 px-1">MYSQLDUMP_PATH</code> in <code class="bg-white dark:bg-gray-800 px-1">.env</code> (the default matches the standard XAMPP path). Sends a WhatsApp alert to the business owner if the backup fails.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-cyan-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">Tracking the number of pages printed</h3>
                                    <p class="text-sm">Every print job has its pages actually counted (from the final PDF file, or one page per image) and aggregated in the "Pages printed" column for each printer (the "Printers" page) — a useful rough estimate for planning ink/paper consumption, not an official precise counter.</p>
                                </div>
                            </div>
EOT,
    'docs_s16' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">16. Editing and deleting WhatsApp messages (Message Edited / Deleted)</h2>
                            <p class="mb-4">When a customer edits or deletes a message they previously sent via WhatsApp (two well-known WhatsApp features), this is automatically reflected in their conversation on the local system, instead of the old version staying displayed as if nothing happened.</p>
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><strong>Edit:</strong> the displayed message text is replaced with the new text, while the previous text (and any earlier edits) is kept in an internal audit log if needed.</li>
                                    <li><strong>Delete:</strong> the content is replaced with "🚫 This message was deleted" (the same convention WhatsApp itself uses) instead of actually deleting the record — the original text/file remain kept internally.</li>
                                </ul>
                                <p class="text-xs text-gray-500 mt-2">This feature requires the central system to have enabled these two events within your company's webhook settings (the "Connected External Systems" page on the central system) — check with the central system's developer if you don't notice this behavior actually happening despite a real message edit/delete.</p>
                            </div>
EOT,
    'audit_event_login' => 'Logged in',
    'audit_event_logout' => 'Logged out',
    'audit_event_failed_login' => 'Failed login attempt',
    'printer_status_check_warning' => 'This printer genuinely reports its faults (out of paper/ink) via Windows — only enable this after manually confirming it, otherwise the customer may receive a false print confirmation.',
    'printer_windows_name_help' => 'Use exactly the name as it appears in the <code>Get-Printer</code> command on Windows.',
    'printer_approval_mode_help' => <<<'EOT'
"Requires approval": no file matching this printer will be printed automatically — a request is sent to the number
<code>PRINTER_ALERT_PHONE</code> (the admin) via WhatsApp, and is approved either via a button on the
<a href=":url" class="text-indigo-600 hover:underline">Print Jobs</a> page
or by replying on WhatsApp with "وافق طباعة &lt;job number&gt;" or "رفض طباعة &lt;job number&gt;"
(or "ارسل لي الملف طباعة &lt;job number&gt;" to preview it first).
EOT,
    'printer_default_help' => <<<'EOT'
<strong>Default printer:</strong> the printer automatically used for any printable file that arrives via WhatsApp and doesn't match any
<a href=":url" class="text-indigo-600 hover:underline">routing rule</a>
(phone number/keyword/file type). Without a default printer set, any file that doesn't match an explicit rule stays unprinted entirely. Only one printer can be set as default at a time — enabling it for one printer automatically disables it for any other.
EOT,
    'printer_failover_help' => <<<'EOT'
<strong>Automatic failover:</strong> set a "backup printer" for each printer from the dedicated column in the table below — if the latest periodic check (every 10 minutes) shows the original printer is unhealthy (out of paper/ink/disconnected), new print jobs are automatically routed to the backup (provided it's itself healthy and enabled), with a WhatsApp alert to the admin about the switch. The system doesn't automatically retry the original printer once it's back — every new job is evaluated fresh when it arrives.
EOT,
    'printer_direct_print_help' => <<<'EOT'
<strong>Direct local printing (no WhatsApp):</strong> place any file inside
<code>:path\print\&lt;printer name&gt;\</code>
and it will be printed automatically (or wait for approval) according to that printer's "print mode" above — the exact path for each printer is shown under its name in the table below, and is created automatically on the first run of the <code>monitor:folder</code> command.
EOT,
    'printer_direct_folder_tooltip' => 'Direct local printing folder for this printer',

    'docs_s17' => <<<'EOT'

                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">17. Separating the webhook verification token from the outbound connection token (CENTRAL_WEBHOOK_TOKEN)</h2>
                            <p class="mb-4">Previously the local system used a single token (<code>CENTRAL_API_TOKEN</code>) for two completely different purposes: outbound connections to the central system, and verifying that incoming webhook requests genuinely come from the central system. Merging both directions into one secret goes against security best practice — separating credentials by trust boundary — because any leak in either direction (logs, support, etc.) exposes both sides at once, and neither can be rotated independently of the other.</p>
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg mb-4">
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><strong>CENTRAL_API_TOKEN (outbound):</strong> sent by this system as a Bearer token when calling the central system's APIs (uploading messages, updating status, etc.). Stays as-is, unchanged.</li>
                                    <li><strong>CENTRAL_WEBHOOK_TOKEN (inbound, new):</strong> an independent token the local system uses to verify that any request arriving on the <code>/api/webhook/*</code> routes genuinely comes from the central system, and not a third party.</li>
                                </ul>
                            </div>
                            <p class="mb-2 font-medium">Activation steps (optional, fully backward-compatible):</p>
                            <ol class="list-decimal list-inside text-sm space-y-1 mr-4 mb-4">
                                <li>From the central system, open your company's "Connected External Systems" page and locate the webhook endpoint linked to this local system (not the company's general secret token), then copy its "Token" value — the central system already supports an independent token per webhook endpoint.</li>
                                <li>From this system, open <a href="{{ route('settings.index') }}" class="text-indigo-600 hover:underline">Settings</a> then the "Central API" card, and paste the value into the "Incoming webhook verification token (CENTRAL_WEBHOOK_TOKEN)" field.</li>
                                <li>Save the settings. From this point on, the local system verifies incoming webhook requests with this independent token instead of falling back to CENTRAL_API_TOKEN.</li>
                            </ol>
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg mb-4">
                                <p class="text-xs text-gray-500">If the field is left empty, the system keeps accepting the old token (CENTRAL_API_TOKEN) for webhook verification as it always has — no immediate action needed, and no existing integration will break. The separation is only recommended as an extra hardening step, not required to keep working.</p>
                            </div>
                            <p class="mb-2 font-medium">Additional signature verification (X-Webhook-Signature):</p>
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <p class="text-sm mb-2">Alongside the token, the central system signs every webhook request with an HMAC-SHA256 signature (the <code>X-Webhook-Signature</code> header) computed from the same token above — proving the request body wasn't altered in transit, not just that the sender holds the correct token. The local system automatically verifies this signature whenever the header is present, and rejects the request (401) if it doesn't match. Older requests or ones from external systems that don't send this header keep working with the token alone (backward compatible).</p>
                                <p class="text-xs text-gray-500">Technical note: the signature used to be computed with a different JSON encoding than the one actually sent in the request body, which made signature verification automatically fail with any Arabic content. This has been fixed on the central system so the request body is sent with the exact same literal string that was signed.</p>
                            </div>
EOT,
];
