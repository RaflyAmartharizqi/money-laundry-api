<?php
    namespace App\Constants;

    class Messages
    {
        const SUCCESS_LOGIN = 'Login successful';
        const SUCCESS_REGISTER = 'Register succesful';

        const SUCCESS_GET_DATA = 'Get data successful';
        const SUCCESS_CREATE_DATA = 'Create data successful';
        const SUCCESS_UPDATE_DATA = 'Update data successful';
        const SUCCESS_DELETE_DATA = 'Delete data successful';

        const SUCCESS_OTP_SENT = "OTP sent to your email";
        const SUCCESS_CREATED = 'Data created successfully';
        const SUCCESS_UPDATED = 'Data updated successfully';
        const SUCCESS_DELETED = 'Data deleted successfully';
        const SUCCESS_EMAIL_VERIFIED = 'Email has been verified';
        const SUCCESS_OTP_RESENT = 'OTP has been resent successfully';
        const SUCCESS_OTP_VERIFIED = 'OTP has been verified';
        const SUCCESS_PASSWORD_RESET = 'Password has been reset successfully';

        const SUCCESS_TRANSACTION_MEMBER = 'Transaction Member successful';
        const SUCCESS_CREATE_TRANSACTION_ORDER = 'Create Transaction Order successful';

        const SUCCESS_SEND_WA = 'Send WA successful';
        const SUCCESS_UPDATE_PAYMENT = 'Update Payment successful';
        const SUCCESS_UPDATE_STATUS_ORDER = 'Update Status Order successful';

        const SUCCESS_MAX_PACKAGE = 'You can add more package';

        const ERROR_NOT_FOUND = 'Data not found';

        const ERROR_INVALID_OTP = 'Invalid OTP';

        const ERROR_VALIDATION = 'Validation error';
        const ERROR_UNAUTHORIZED = 'Unauthorized';
        const ERROR_REGISTER = 'Register Failed';

        const ERROR_GET_DATA = 'Get data failed';
        const ERROR_CREATE_DATA = 'Create data failed';
        const ERROR_UPDATE_DATA = 'Update data failed';
        const ERROR_DELETE_DATA = 'Delete data failed';

        const ERROR_TRANSACTION_MEMBER = 'Transaction Member failed';
        const ERROR_CREATE_TRANSACTION_ORDER = 'Error Transaction Order failed';

        const ERROR_SEND_WA = 'Error send WA';
        const ERROR_UPDATE_PAYMENT = 'Error update payment';
        const ERROR_UPDATE_STATUS_ORDER = 'Error update status order';
        const ERROR_MAX_PACKAGE = 'Maximum package reached';
    }
?>