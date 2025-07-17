<?php

if (!function_exists('getDefaultEmailBody')) {
    /**
     * Generate the default email body with dynamic content.
     *
     * @param array $data
     * @return string
     */
    function getDefaultEmailBody($data)
    {
        // Extract dynamic values from the data array
        $contactFirstName = $data->name ?? 'Customer';
        $invoiceNumber = $data->id ?? 'N/A';
        $invoiceStatus = getStatusText($data->payment_status ?? null);
        $emailSignature = getGarageDetails()->garage_name;
        $website_prefix_code = get_option('website_prefix_code');

        // Define the styled HTML content
        return "
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Dear {$contactFirstName},
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Thank you for your custom.
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                We have prepared the following invoice for you: <strong>#{$website_prefix_code}{$invoiceNumber}</strong>
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Invoice status: 
                <span style=\"background-color: " . getInvoiceStatusColor($data->payment_status ?? null) . "; color: #fff; padding: 2px 5px;\">{$invoiceStatus}</span>
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Please find the attachment below.
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Kind Regards,<br>
                {$emailSignature}
            </p>
        ";
    }
}

if (!function_exists('getEstimateEmailBody')) {
    /**
     * Generate the default email body with dynamic content.
     *
     * @param array $data
     * @return string
     */
    function getEstimateEmailBody($data)
    {
        // Extract dynamic values from the data array
        $contactFirstName = $data->name ?? 'Customer';
        $invoiceNumber = $data->id ?? 'N/A';
        $invoiceStatus = getStatusText($data->payment_status ?? null);
        $emailSignature = getGarageDetails()->garage_name;
        $website_prefix_code = get_option('website_prefix_code');

        // Define the styled HTML content
        return "
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Dear {$contactFirstName},
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Thank you for your custom.
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                We have prepared the following Estimate for you: <strong>#EST-{$website_prefix_code}{$invoiceNumber}</strong>
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Please find the attachment below.
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Kind Regards,<br>
                {$emailSignature}
            </p>
        ";
    }
}

if (!function_exists('getStatementEmailBody')) {
    /**
     * Generate the default email body with dynamic content.
     *
     * @param array $data
     * @return string
     */
    function getStatementEmailBody($data)
    {
        // Extract dynamic values from the data array
        $contactFirstName = $data->name ?? 'Customer';
        $statementNumber = $data->id ?? 'N/A';
        $invoiceStatus = getStatusText($data->payment_status ?? null);
        $emailSignature = getGarageDetails()->garage_name;

        // Define the styled HTML content
        return "
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Dear {$contactFirstName},
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Thank you for your custom.
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                We have prepared the following Statement for you: <strong>STAT #{$statementNumber}</strong>
            </p>

            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Please find the attachment below.
            </p>
            <p style=\"font-family: Arial, sans-serif; font-size: 14px; color: #333;\">
                Kind Regards,<br>
                {$emailSignature}
            </p>
        ";
    }
}

if (!function_exists('getStatusText')) {
    /**
     * Get the text representation of the invoice status.
     *
     * @param string|null $status
     * @return string
     */
    function getStatusText($status)
    {
        switch ($status) {
            case '1':
                return 'Paid';
            case '0':
                return 'Unpaid';
            case '2':
                return 'Partially Paid';
            case '3':
                return 'Overdue';
            default:
                return 'Unknown';
        }
    }
}

if (!function_exists('getInvoiceStatusColor')) {
    /**
     * Get the background color for the invoice status based on its value.
     *
     * @param string|null $status
     * @return string
     */
    function getInvoiceStatusColor($status)
    {
        switch ($status) {
            case '1': // Paid
                return '#2dc66b'; // Green
            case '0': // Unpaid
                return '#ff4d4d'; // Red
            case '2': // Partially Paid
                return '#ffcc00'; // Yellow
            case '3': // Overdue
                return '#ff6666'; // Light Red
            default:
                return '#cccccc'; // Gray (default/unknown)
        }
    }
}