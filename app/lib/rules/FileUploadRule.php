<?php

// namespace app\lib\rules;

// use app\contracts\RuleInterface;

// class FileUploadRule implements RuleInterface
// {
//     private $errorCode; 

//     public function passes($value): bool
//     {
//         $result = isset($value['error']) && $value['error'] === UPLOAD_ERR_OK;
//         $this->errorCode = $value['error'];
        
//         return $result;
//     }
    
//     public function message(): string
//     {
//         switch ($this->errorCode) {
//             case UPLOAD_ERR_INI_SIZE:
//                 $messageKey = 'upload_error_1';
//                 break;
//             case UPLOAD_ERR_FORM_SIZE:
//                 $messageKey = 'upload_error_2';
//                 break;
//             case UPLOAD_ERR_PARTIAL:
//                 $messageKey = 'upload_error_3';
//                 break;
//             case UPLOAD_ERR_NO_FILE:
//                 $messageKey = 'upload_error_4';
//                 break;
//             case UPLOAD_ERR_NO_TMP_DIR:
//                 $messageKey = 'upload_error_6';
//                 break;
//             case UPLOAD_ERR_CANT_WRITE:
//                 $messageKey = 'upload_error_7';
//                 break;
//             case UPLOAD_ERR_EXTENSION:
//                 $messageKey = 'upload_error_8';
//                 break;
//             default:
//                 $messageKey = 'upload_error_unknown';
//         }

//         return rule_message($messageKey);
//     }

// }
